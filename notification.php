<?php

require_once('notification_base.php');
require_once('notification_type.php');
require_once('notification_delivery_type.php');
require_once('notification_subscription.php');


/**
 * Creates and sends notifications to subscribed users
 *
 * @method static notification[] findAll()
 * @method static notification findById()
 *
 * @property $author string
 * @property $target_rid int
 * @property $target_uid int
 * @property $target_gid string
 * @property $target_forum_uid string
 * @property $target_email string
 * @property $target_phone string
 * @property $type_id int
 * @property $delivery_type_id int
 * @property $message string
 * @property $status int
 * @property $priority int
 * @property $created string
 * @property $ip string
 * @property $sent string
 */
class notification extends notification_base
{

    /**
     * @var string
     */
    static public $table = 'notification';

    /**
     * @var array
     */
    static public $fields = array(
        'author',
        'target_rid',
        'target_uid',
        'target_gid',
        'target_forum_uid',
        'target_email',
        'target_phone',
        'type_id',
        'delivery_type_id',
        'message',
        'status',
        'priority',
        'created',
        'ip',
        'sent',
    );

    /**
     * Save a notification to users who are subscribed to this type
     *
     * @param $author
     * @param $type_id
     * @param $message
     * @param $priority
     * @return bool
     */
    static public function create($author, $type_id, $message, $priority)
    {
        // find all the subscriptions to this notification type
        $subscriptions = notification_subscription::findAll("`type_id`='" . (int)$type_id . "'");
        foreach ($subscriptions as $subscription) {

            // create new notification
            $notification = new notification();

            // populate notification attributes
            $notification->author = $author;
            $notification->type_id = $type_id;
            $notification->message = $message;
            $notification->priority = $priority;

            // populate subscription attributes
            $notification->target_rid = $subscription->rid;
            $notification->target_uid = $subscription->uid;
            $notification->target_gid = $subscription->gid;
            $notification->target_forum_uid = $subscription->forum_uid;
            $notification->target_email = $subscription->email;
            $notification->target_phone = $subscription->phone;
            $notification->delivery_type_id = $subscription->delivery_type_id;

            // populate log info
            $notification->created = date('Y-m-d H:i:s');
            $notification->ip = $_SERVER['REMOTE_ADDR'];

            // save to database
            if (!$notification->save()) {
                return false;
            }

        }
        return true;
    }

    /**
     * Sends all pending notifications
     *
     */
    static public function spool()
    {
        // find all notifications that are ready to be sent
        $notifications = notification::findAll('`status`=0');
        foreach ($notifications as $notification) {
            // send the notification
            if ($notification->send()) {
                // set success
                $notification->status = 1;
            }
            else {
                // set error
                $notification->status = -1;
            }
            // set the timestamp
            $notification->sent = date('Y-m-d H:i:s');
            // save the record
            $notification->save();
        }
    }

    /**
     * Send a notification
     *
     * @return bool
     */
    private function send()
    {
        // find which type of notification we are sending, then send
        $deliveryType = new notification_delivery_type($this->type_id);
        switch ($deliveryType->name) {
            case 'Mail':
                $rid = $this->target_rid;
                $uid = $this->target_uid;
                return notification_delivery_type::sendMail($rid, $uid, $this->message);

            case 'Email':
                $email = $this->target_email;
                return notification_delivery_type::sendEmail($email, $this->message);

            case 'PM':
                $forum_uid = $this->target_forum_uid;
                return notification_delivery_type::sendPM($forum_uid, $this->message);

            case 'Announcement':
                $rid = $this->target_rid;
                return notification_delivery_type::sendAnnouncement($rid, $this->message);

            case 'GMAnnouncement':
                $rid = $this->target_rid;
                return notification_delivery_type::sendGMAnnouncement($rid, $this->message);

            case 'SMS':
                $phone = $this->target_phone;
                return notification_delivery_type::sendSMS($phone, $this->message);
        }
        return false;
    }

}