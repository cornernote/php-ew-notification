<?php
/**
 * PHP Notifications
 *
 * @author Brett O'Donnell - cornernote@gmail.com
 * @copyright 2013, All Rights Reserved
 */

require_once('notification.php');

$author = 'cornernote';
$type_id = 1; // Tickets
$message = 'Hello World!';
$priority = 1;
notification::create($author, $type_id, $message, $priority);
