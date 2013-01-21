<?php

/**
 * @method static notification_delivery_type[] findAll()
 * @method static notification_delivery_type findById()
 *
 * @property $name string
 */
class notification_delivery_type extends notification_base
{

    /**
     * @var string
     */
    static public $table = 'notification_delivery_type';

    /**
     * @var array
     */
    static public $fields = array(
        'name',
    );

    /**
     * @param $rid
     * @param $uid
     * @param $message
     * @return bool
     */
    static public function sendMail($rid, $uid, $message)
    {
        $cmd = new tcsoap_commands($rid);
        $data = $cmd->sendMail($uid, 'Notification', $message);
        return ($data->getStatus == 1) ? true : false;
    }

    /**
     * @param $email
     * @param $message
     * @return bool
     */
    static public function sendEmail($email, $message)
    {
        //$email = new EMail();
        //$email->SendMail("Notification", $email, "Eternal WoW! Notifications <notifications@eternal-wow.com>", $message);
        return false;
    }

    /**
     * @param $recipient
     * @param $message
     * @return bool
     */
    static public function sendPM($recipient, $message)
    {
        sendphpbbpm($message, $recipient, 'Notification');
        return true;
    }

    /**
     * @param $rid
     * @param $message
     * @return bool
     */
    static public function sendAnnouncement($rid, $message)
    {
        $cmd = new tcsoap_commands($rid);
        $data = $cmd->announce($message);
        return ($data->getStatus == 1) ? true : false;
    }

    /**
     * @param $rid
     * @param $message
     * @return bool
     */
    static public function sendGMAnnouncement($rid, $message)
    {
        $cmd = new tcsoap_commands($rid);
        $data = $cmd->gmAnnounce($message);
        return ($data->getStatus == 1) ? true : false;
    }

    /**
     * @param $phone
     * @param $message
     * @return bool
     */
    static public function sendSMS($phone, $message)
    {
        return false;
    }

}
