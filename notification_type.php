<?php

/**
 * @method static notification[] findAll()
 * @method static notification findById()
 *
 * @property $name string
 * @property $priority int
 * @property $target_page string
 */
class notification_type extends notification_base
{
    /**
     * @var array
     */
    static public $fields = array(
        'name',
        'priority',
        'target_page',
    );

}
