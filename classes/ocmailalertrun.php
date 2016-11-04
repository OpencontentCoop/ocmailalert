<?php

use Opencontent\QueryLanguage\Parser;
use Opencontent\Opendata\Api\Values\SearchResults;
use Opencontent\Opendata\Api\ContentSearch;

class OCMailAlertRun
{
    /**
     * @var OCMailAlert
     */
    private $alert;

    private $now;

    private $verbose;

    public static function runAll($verbose)
    {
        $alerts = OCMailAlert::fetchList();
        foreach ($alerts as $alert) {
            $runAlert = new OCMailAlertRun($alert, $verbose);
            $runAlert->run();
        }
    }

    public function __construct(OCMailAlert $alert, $verbose = true)
    {
        $this->alert = $alert;
        $this->now = time();
        $this->verbose = $verbose;

        if ($this->verbose){
            eZCLI::instance()->output("Process {$this->alert->attribute('label')}");
        }
    }

    public function run()
    {
        $lastCall = $this->alert->attribute('last_call');
        try {
            $this->log('output', "Last call: " . date('c', $lastCall));
            $this->log('output', "Frequency: {$this->alert->attribute('frequency')}");

            if ($this->checkFrequency()) {

                $this->log('output', "Query: {$this->alert->attribute('query')} ");

                $searchResults = $this->getQueryResult();
                $result = $this->evaluateQueryResult($searchResults);

                $this->log('output', "Result: " . var_export($result, 1));

                $this->alert->setAttribute('last_call', $this->now);
                $this->alert->setAttribute('last_log', var_export($result, 1));
                $this->alert->store();

                if ($result) {
                    $this->log('output', "Send mail");
                    $this->sendMail($searchResults);
                }
            }
        } catch (Exception $e) {
            $this->alert->setAttribute('last_call', $lastCall);
            $this->alert->setAttribute('last_log', 'Error: ' . $e->getMessage());
            $this->alert->store();

            $this->log('output');
            $this->log('error', $e->getMessage());
        }
        $this->log('output');
    }

    private function log($type, $message = '', $eol = true)
    {
        if ($this->verbose){
            eZCLI::instance()->{$type}("    " . $message, $eol);
        }
        if ($message != '') {
            eZLog::write("[{$this->alert->attribute('label')}] $message", 'ocmailalert.log');
        }
    }

    private function checkFrequency()
    {
        $lastTime = $this->alert->attribute('last_call');
        $frequency = $this->alert->attribute('frequency');
        $now = mktime(0, 0, 0);
        if ($lastTime > 0) {
            $diff = $now - $lastTime;
            switch ($frequency) {
                case OCMailAlertUtils::FREQUENCY_MONTHLY: {
                    if ($diff < 2629744) {
                        return false;
                    }
                }
                    break;

                case OCMailAlertUtils::FREQUENCY_WEEKLY: {
                    if ($diff < 604800) {
                        return false;
                    }
                }
                    break;

                case OCMailAlertUtils::FREQUENCY_DAILY: {
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

    private function getQueryResult()
    {
        $query = $this->alert->attribute('query');
        $contentSearch = new ContentSearch();
        $contentSearch->setCurrentEnvironmentSettings(new DefaultEnvironmentSettings());

        return $contentSearch->search($query);

    }

    private function evaluateQueryResult(SearchResults $searchResults)
    {
        $resultCount = $searchResults->totalCount;

        $operators = OCMailAlertUtils::conditionOperators();
        $condition = $operators[$this->alert->attribute('condition')];
        $conditionFunction = $condition['call'];

        return $conditionFunction((int)$resultCount, (int)$this->alert->attribute('condition_value'));
    }

    private function sendMail(SearchResults $searchResults)
    {
        $receivers = $this->alert->attribute('recipients_address');
        $subject = $this->alert->attribute('subject');

        $ini = eZINI::instance();
        $emailSender = $ini->variable('MailSettings', 'EmailSender');
        if (!$emailSender) {
            $emailSender = $ini->variable('MailSettings', 'AdminEmail');
        }

        $mail = new eZMail();

        $mail->setContentType('text/html');

        $mail->setSender($emailSender);
        $this->log('output', "    Sender: $emailSender");

        $i = 0;
        foreach ($receivers as $receiver) {
            if ($i == 0) {
                $mail->setReceiver($receiver);
            } else {
                $mail->addReceiver($receiver);
            }
            $i++;
        }
        $this->log('output', "    Receivers: " . implode(', ', $receivers));

        $mail->setSubject($subject);

        $tpl = eZTemplate::factory();
        $tpl->setVariable('alert', $this->alert);
        $tpl->setVariable('search_results', (array)$searchResults);
        $tpl->setVariable('language', eZLocale::currentLocaleCode());
        $body = $tpl->fetch('design:ocmailalert_mail.tpl');
        $mail->setBody($body);

        if (!eZMailTransport::send($mail)) {
            throw new Exception("Can not send mail");
        }
    }
}
