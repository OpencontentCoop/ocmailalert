<?php

use Opencontent\Opendata\Api\QueryLanguage\EzFind\QueryBuilder;
use Opencontent\QueryLanguage\Parser;
use Opencontent\QueryLanguage\Query;
use Opencontent\QueryLanguage\Converter\AnalyzerQueryConverter;
use Opencontent\Opendata\Api\ContentSearch;

class OCMailAlertUtils
{
    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';

    public static function frequencies()
    {
        return array(
            self::FREQUENCY_DAILY => ezpI18n::tr('extension/ocmailalert', 'Daily'),
            self::FREQUENCY_WEEKLY => ezpI18n::tr('extension/ocmailalert', 'Weekly'),
            self::FREQUENCY_MONTHLY => ezpI18n::tr('extension/ocmailalert', 'Monthly'),
        );
    }

    public static function validateData(array $data)
    {
        if (!isset( $data['label'] ) || empty( $data['label'] )) {
            throw new Exception("Field label is required");
        }

        if (!isset( $data['frequency'] ) || empty( $data['frequency'] )) {
            throw new Exception("Field frequency is required");
        } elseif (!array_key_exists($data['frequency'], self::frequencies())) {
            throw new Exception("Field frequency is not valid");
        }

        if (!isset( $data['query'] ) || empty( $data['query'] )) {
            throw new Exception("Field query is required");
        } else {
            try {
                self::validateQuery($data['query']);
            } catch (Exception $e) {
                throw new Exception("Query is not valid: " . $e->getMessage());
            }
        }

        if (!isset( $data['match_condition'] ) || empty( $data['match_condition'] )) {
            throw new Exception("Field match_condition is required");
        }

        if (!isset( $data['match_condition_value'] ) || $data['match_condition_value'] == '') {
            throw new Exception("Field match_condition_value is required");
        }

        if (!isset( $data['recipients'] ) || empty( $data['recipients'] )) {
            throw new Exception("Field recipients is required");
        } else {
            $emails = self::splitMailAddresses($data['recipients']);
            foreach ($emails as $email) {
                if (!eZMail::validate($email)) {
                    throw new Exception("Address $email is not valid");
                }
            }
        }

        if (!isset( $data['subject'] ) || empty( $data['subject'] )) {
            throw new Exception("Field subject is required");
        }

        if (!isset( $data['body'] ) || empty( $data['body'] )) {
            throw new Exception("Field body is required");
        }
    }

    public static function splitMailAddresses($data)
    {
        $emails = explode("\n", $data);
        $emails = array_map('trim', $emails);

        return $emails;
    }

    public static function validateQuery($query)
    {
        $builder = new QueryBuilder();

        $queryObject = $builder->instanceQuery($query);
        $queryObject->convert();

        $tokenFactory = $builder->getTokenFactory();
        $parser = new Parser(new Query($query));
        $query = $parser->setTokenFactory($tokenFactory)->parse();

        $converter = new AnalyzerQueryConverter();
        $converter->setQuery($query);

        $analysis = $converter->convert();

        return $analysis;
    }

    public static function conditionOperators()
    {
        return array(
            'eq' => array(
                'name' => '=',
                'call' => function ($match, $value) {
                    return $match == $value;
                }
            ),
            'gt' => array(
                'name' => '>',
                'call' => function ($match, $value) {
                    return $match > $value;
                }
            ),
            'ge' => array(
                'name' => '>=',
                'call' => function ($match, $value) {
                    return $match >= $value;
                }
            ),
            'lt' => array(
                'name' => '<',
                'call' => function ($match, $value) {
                    return $match < $value;
                }
            ),
            'le' => array(
                'name' => '<=',
                'call' => function ($match, $value) {
                    return $match <= $value;
                }
            ),
        );
    }

    public static function conditionOperatorNames()
    {
        $operators = array();
        foreach (self::conditionOperators() as $identifier => $operator) {
            $operators[$identifier] = $operator['name'];
        }

        return $operators;
    }

    public static function conditionOperatorName($identifier)
    {
        $operators = self::conditionOperators();

        return $operators[$identifier]['name'];
    }

}
