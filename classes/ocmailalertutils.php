<?php

use Opencontent\Opendata\Api\QueryLanguage\EzFind\QueryBuilder;
use Opencontent\QueryLanguage\Parser;
use Opencontent\QueryLanguage\Query;
use Opencontent\QueryLanguage\Converter\AnalyzerQueryConverter;
use Opencontent\Opendata\Api\EnvironmentLoader;
use Opencontent\Opendata\Api\ContentSearch;
use Opencontent\Opendata\Api\ClassRepository;

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

        if (!isset( $data['condition'] ) || empty( $data['condition'] )) {
            throw new Exception("Field condition is required");
        }

        if (!isset( $data['condition_value'] ) || $data['condition_value'] == '') {
            throw new Exception("Field condition_value is required");
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

    public static function run($isQuiet = false)
    {
        $alerts = OCMailAlert::fetchList();
        foreach ($alerts as $alert) {
            $now = time();
            $lastCall = $alert->attribute('last_call');
            try {
                if (!$isQuiet) {
                    eZCLI::instance()->notice("Process {$alert->attribute('label')}");
                    eZCLI::instance()->notice("  Last call: " . date('c', $lastCall));
                    eZCLI::instance()->notice("  Frequency: {$alert->attribute('frequency')}");
                }
                if (self::checkFrequency($alert)) {
                    if (!$isQuiet) {
                        eZCLI::instance()->notice("  Query: {$alert->attribute('query')}: ", false);
                    }
                    $result = self::checkQueryResult($alert);
                    if (!$isQuiet) {
                        eZCLI::instance()->notice(var_export($result, 1));
                    }

                    $alert->setAttribute('last_call', $now);
                    $alert->setAttribute('last_log', var_export($result, 1));
                    $alert->store();

                    if ($result) {
                        eZCLI::instance()->notice("  Send mail");
                        self::sendMail($alert);
                    }
                }
            } catch (Exception $e) {
                $alert->setAttribute('last_call', $lastCall);
                $alert->setAttribute('last_log', 'Error: ' . $e->getMessage());
                $alert->store();
                if (!$isQuiet) {
                    eZCLI::instance()->notice();
                    eZCLI::instance()->error($e->getMessage());
                }
            }
        }
    }

    private static function checkFrequency(OCMailAlert $alert)
    {
        $lastTime = $alert->attribute('last_call');
        $frequency = $alert->attribute('frequency');
        $now = mktime(0, 0, 0);
        if ($lastTime > 0) {
            $diff = $now - $lastTime;
            switch ($frequency) {
                case self::FREQUENCY_MONTHLY: {
                    if ($diff < 2629744) {
                        return false;
                    }
                }
                    break;

                case self::FREQUENCY_WEEKLY: {
                    if ($diff < 604800) {
                        return false;
                    }
                }
                    break;

                case self::FREQUENCY_DAILY: {
                    if ($diff < 86400) {
                        return false;
                    }
                }
                    break;

                default:
                    return false;
            }
        }

        return true;
    }

    private static function checkQueryResult(OCMailAlert $alert)
    {
        $query = $alert->attribute('query');
        $contentSearch = new ContentSearch();
        $contentSearch->setCurrentEnvironmentSettings(new DefaultEnvironmentSettings());

        $searchResults = $contentSearch->search($query);
        $resultCount = $searchResults->totalCount;

        $operators = self::conditionOperators();
        $condition = $operators[$alert->attribute('condition')];
        $conditionFunction = $condition['call'];

        return $conditionFunction((int)$resultCount, (int)$alert->attribute('condition_value'));
    }

    private static function sendMail(OCMailAlert $alert)
    {
        $receivers = $alert->attribute('recipients_address');
        $subject = $alert->attribute('subject');
        $body = $alert->attribute('body');

        $ini = eZINI::instance();
        $emailSender = $ini->variable('MailSettings', 'EmailSender');
        if (!$emailSender) {
            $emailSender = $ini->variable('MailSettings', 'AdminEmail');
        }

        $mail = new eZMail();

        $mail->setSender($emailSender);

        $i = 0;
        foreach ($receivers as $receiver) {
            if ($i == 0) {
                $mail->setReceiver($receiver);
            } else {
                $mail->addReceiver($receiver);
            }

            $i++;
        }

        $mail->setSubject($subject);
        $mail->setBody($body);

        if (!eZMailTransport::send($mail)) {
            throw new Exception("Can not send mail");
        }
    }

}
