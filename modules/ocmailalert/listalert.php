<?php

/** @var eZModule $Module */
$Module = $Params['Module'];
$Result = array();
$tpl = eZTemplate::factory();

try {
    $offset = isset( $Params['UserParameters']['offset'] ) ? (int)$Params['UserParameters']['offset'] : 0; // Offset for pagination
    $limit = 50;
    $alerts = OCMailAlert::fetchList($offset, $limit);
    $alertCount = OCMailAlert::count(OCMailAlert::definition());
    $currentURI = '/' . $Module->currentModule() . '/' . $Module->currentView();

    $tpl->setVariable('alerts', $alerts);
    $tpl->setVariable('offset', $offset);
    $tpl->setVariable('limit', $limit);
    $tpl->setVariable('uri', $currentURI);
    $tpl->setVariable('alert_count', $alertCount);
    $tpl->setVariable('view_parameters', $Params['UserParameters']);
} catch (Exception $e) {
    $errMsg = $e->getMessage();
    $tpl->setVariable('error_message', $errMsg);
}

$Result['path'] = array(
    array(
        'url' => false,
        'text' => ezpI18n::tr('extension/ocmailalert', 'Alert management list')
    )
);
$Result['left_menu'] = 'design:ocmailalert/parts/leftmenu.tpl';
$Result['content'] = $tpl->fetch('design:ocmailalert/list.tpl');
