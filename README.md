## PHP Classe to access Kinvey via REST ##
This class is useful to developers how want to access Kinvey DataStore via PHP. 
While we don't have an PHP Kinvey API, I thinks that this class will help you, becouse help me.

## Below some examples ##

<?php
require_once 'KinveyREST.php';
require_once 'debug.php';

/**
 * Init class
 */

use KinveyREST\KinveyREST;

$kinveyRest = New KinveyREST();

/**
 * Get users
 */
$users = $kinveyRest->users(null, null, array('fields' => array('name', 'email', '_id')));

/**
 * Retrieve
 */
$data = $kinveyRest->retrieve('Albums',
    '',
    array(),
    array('sort' => array('_kmd.lmt' => -1),
        'limit' => 2),
    array('pictures'), true/* Convert result to json */);


/**
 * Creating
 */
$today = new \DateTime('now');
$kinveyDataFormat = $today->format(\DateTime::ATOM); // Kinvey data format

$data = $kinveyRest->create("TodoList", array('task' => 'Create CRUD',
                                              'done' => false,
                                              'complexity' => 2,
                                              'expirationAt' => $kinveyDataFormat,
                                              '_acl' => array('creator' => '53a320d1c71d0b672103d014') /*If you need to save the register to a specified owner - user_id like 53a320d1c71d0b672103d014*/), true);




