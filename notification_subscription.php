<?php

/**
 * @method static notification_subscription[] findAll()
 * @method static notification_subscription findById()
 *
 * @property $uid int
 * @property $rid int
 * @property $gid int
 * @property $forum_uid int
 * @property $email int
 * @property $phone int
 * @property $type_id int
 * @property $delivery_type_id int
 * @property $created string
 * @property $updated string
 */
class notification_subscription extends notification_base
{

    /**
     * @var string
     */
    static public $table = 'notification_subscription';

    /**
     * @var array
     */
    static public $fields = array(
        'uid',
        'rid',
        'gid',
        'forum_uid',
        'email',
        'phone',
        'type_id',
        'delivery_type_id',
        'created',
        'updated',
    );

}
