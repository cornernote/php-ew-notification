<?php

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
     * @var array
     */
    static public $fields = array(
        'name',
        'priority',
        'target_page',
    );

}
