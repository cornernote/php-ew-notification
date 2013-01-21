<?php
/**
 * PHP Notifications
 *
 * @author Brett O'Donnell - cornernote@gmail.com
 * @copyright 2013, All Rights Reserved
 */

/**
 * @method static notification_type[] findAll()
 * @method static notification_type findById()
 *
 * @property $name string
 * @property $priority int
 * @property $target_page string
 */
class notification_type extends notification_base
{

    /**
     * @var string
     */
    static public $table = 'notification_type';

    /**
     * @var array
     */
    static public $fields = array(
        'name',
    );

}
