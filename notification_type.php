<?php

/**
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
    protected $fields = array(
        'name',
        'priority',
        'target_page',
    );

}
