<?php

/** @var eZModule $Module */
$Module = $Params['Module'];
$Id = $Params['Id'];

$alert = OCMailAlert::fetch((int)$Id);
if ($alert instanceof OCMailAlert) {

    $Result = array();
    $tpl = eZTemplate::factory();

    $tpl->setVariable('alert', $alert);

    $Result['path'] = array(
        array(
            'url' => false,
            'text' => ezpI18n::tr('extension/ocmailalert', 'Alert management list')
        )
    );
    $Result['left_menu'] = 'design:ocmailalert/parts/leftmenu.tpl';
    $Result['content'] = $tpl->fetch('design:ocmailalert/detail.tpl');

}else{
    $Module->redirectToView('list');
    return;
}
