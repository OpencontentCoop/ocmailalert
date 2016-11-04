<?php

/** @var eZModule $Module */
$Module = $Params['Module'];
$Id = $Params['Id'];

$alert = OCMailAlert::fetch((int)$Id);
if ($alert instanceof OCMailAlert) {
    $alert->setAttribute('last_call', 0);
    $alert->setAttribute('last_log', '');
    $alert->store();
}

$Module->redirectToView('list');
return;
