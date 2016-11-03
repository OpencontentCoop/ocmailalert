<?php

/** @var eZModule $Module */
$Module = $Params['Module'];
$Id = $Params['Id'];

$alert = OCMailAlert::fetch((int)$Id);
if ($alert instanceof OCMailAlert) {
   $alert->remove();
}

$Module->redirectToView('list');
return;
