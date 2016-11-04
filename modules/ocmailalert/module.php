<?php

$Module = array('name' => 'ocmailalert');

$ViewList = array();

$ViewList['list'] = array(
    'script' => 'list.php',
    'params' => array(),
    'unordered_params' => array(),
    'single_post_actions' => array(),
    'post_action_parameters' => array(),
    'default_navigation_part' => 'ocmailalertnavigationpart',
    'functions' => array('manage')
);


$ViewList['addalert'] = array(
    'script' => 'addalert.php',
    'params' => array('Id'),
    'unordered_params' => array(),
    'default_navigation_part' => 'ocmailalertnavigationpart',
    'functions' => array('manage')
);

$ViewList['removealert'] = array(
    'script' => 'removealert.php',
    'params' => array('Id'),
    'unordered_params' => array(),
    'default_navigation_part' => 'ocmailalertnavigationpart',
    'functions' => array('manage')
);

$ViewList['resetalert'] = array(
    'script' => 'resetalert.php',
    'params' => array('Id'),
    'unordered_params' => array(),
    'default_navigation_part' => 'ocmailalertnavigationpart',
    'functions' => array('manage')
);

$FunctionList['manage'] = array();
