<?php

require_once('notification_base.php');
require_once('notification_type.php');
require_once('notify.php');


/**
 * @method notification[] findAll()
 * @method notification findById()
 *
 * @property $author string
 * @property $target_rid int
 * @property $target_uid int
 * @property $target_group string
 * @property $type int
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
     * @var array
     */
    protected $fields = array(
        'author',
        'target_rid',
        'target_uid',
        'target_group',
        'type',
        'message',
        'status',
        'priority',
        'created',
        'ip',
        'sent',
    );

    /**
     * @param $author
     * @param $type
     * @param $message
     * @param $priority
     * @return mixed
     */
    static public function create($author, $type, $message, $priority)
    {
        $notification = new notification();
        $notification->author = $author;
        $notification->type = $type;
        $notification->message = $message;
        $notification->priority = $priority;
        $notification->created = date('Y-m-d H:i:s');
        $notification->ip = $_SERVER['REMOTE_ADDR'];
        if ($notification->save()) {
            return $notification;
        }
    }

    /**
     *
     */
    public function spool()
    {
        $notifications = $this->findAll('`status`=0');
        foreach ($notifications as $notification) {
            if ($notification->send()) {
                $notification->status = 1;
            }
            else {
                $notification->status = -1;
            }
            $notification->sent = date('Y-m-d H:i:s');
            $notification->save();
        }
    }

    /**
     * @return bool
     */
    private function send()
    {
        $notificationType = new notification_type($this->type);
        switch ($notificationType->name) {
            case 'SendMail':
                $rid = '???';
                $uid = '???';
                return Notify::sendMail($rid, $uid, $this->message);

            case 'SendEmail':
                $email = '???';
                return Notify::sendEmail($email, $this->message);

            case 'SendPM':
                $forum_uid = '???';
                return Notify::sendPM($forum_uid, $this->message);

            case 'sendAnnouncement':
                $rid = '???';
                return Notify::sendAnnouncement($rid, $this->message);

            case 'sendGMAnnouncement':
                $rid = '???';
                return Notify::sendGMAnnouncement($rid, $this->message);

            case 'SendSMS':
                $phone = '???';
                return Notify::sendSMS($phone, $this->message);
        }
        return false;
    }

}