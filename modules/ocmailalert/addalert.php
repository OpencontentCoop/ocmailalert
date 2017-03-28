<?php

/** @var eZModule $Module */
$Module = $Params['Module'];
$Id = $Params['Id'];
$Result = array();
$tpl = eZTemplate::factory();
$http = eZHTTPTool::instance();

$data = array();
$alert = OCMailAlert::fetch((int)$Id);

try {
    if ($http->hasPostVariable('SaveAlertButton')) {
        $data = array(
            'label' => $http->postVariable('label'),
            'frequency' => $http->postVariable('frequency'),
            'query' => $http->postVariable('query'),
            'match_condition' => $http->postVariable('match_condition'),
            'match_condition_value' => $http->postVariable('match_condition_value'),
            'recipients' => $http->postVariable('recipients'),
            'subject' => $http->postVariable('subject'),
            'body' => $http->postVariable('body'),
        );

        OCMailAlertUtils::validateData($data);

        if (!$alert instanceof OCMailAlert) {
            $alert = new OCMailAlert();
        }
        $alert->fromArray($data);
        $alert->store();

        $Module->redirectToView('list');

        return;
    }


} catch (Exception $e) {
    if ($alert instanceof OCMailAlert) {
        $data['id'] = $alert->attribute('id');
    }
    $alert = $data;
    $errMsg = $e->getMessage();
    $tpl->setVariable('error_message', $errMsg);
}

$tpl->setVariable('frequencies', OCMailAlertUtils::frequencies());
$tpl->setVariable('conditions', OCMailAlertUtils::conditionOperatorNames());
$tpl->setVariable('alert', $alert);

$Result['path'] = array(
    array(
        'url' => false,
        'text' => ezpI18n::tr('extension/ocmailalert', 'Configure new alert')
    )
);
$Result['left_menu'] = 'design:ocmailalert/parts/leftmenu.tpl';
$Result['content'] = $tpl->fetch('design:ocmailalert/add.tpl');
