<?php
/**
 * PHP Notifications
 *
 * @author Brett O'Donnell - cornernote@gmail.com
 * @copyright 2013, All Rights Reserved
 */

require_once('../db.php');
require_once('../notification.php');
 
$_ENV['config']['dbconn'] = 'test';
 
$_ENV['DB'] = new SqlDB;
$_ENV['DB']->NewConnection($_ENV['config']['dbconn'], 'localhost', 'root', '', 'eternal', false, false, false);

$author = 'cornernote';
$type_id = 1; // Tickets
$message = 'Hello World!';
$priority = 1;
notification::create($author, $type_id, $message, $priority);
