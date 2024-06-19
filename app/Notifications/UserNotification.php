<?php

namespace App\Notifications;

use App\Models\Notice as ModelsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidMessagePriority;

class UserNotification extends Notification
{
    use Queueable;
    protected ModelsNotification $notification;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        //
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class,'database'];
    }

    public function toFcm($notifiable)
    {
        $image = '';
        if($this->notification->image!=null && $this->notification->image!=''){
            $image = getImage($this->notification->image,'notifications/');
        }
        $noti = \NotificationChannels\Fcm\Resources\Notification::create()
            ->setTitle($this->notification->title)
            ->setBody($this->notification->content);
        if($this->notification->image!=null && $this->notification->image!=''){
            $noti->setImage($image);
        }
        return (new FcmMessage())
            ->setNotification($noti)
            ->setData([
                'title' => $this->notification->title,
                'message' => $this->notification->content,
                'type' => 'general', 
                'business_id' => (string) $this->notification->business_id, 
                'image' => $image
            ])

            ->setAndroid(
                \NotificationChannels\Fcm\Resources\AndroidConfig::create()
                    ->setPriority(AndroidMessagePriority::HIGH())
                    ->setNotification(
                        \NotificationChannels\Fcm\Resources\AndroidNotification::create()
                            ->setColor('#0A0A0A')
                    )
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
            'title' => $this->notification->title,
            'message' => $this->notification->content,
            'business_id' => $this->notification->business_id,
            'image' => $this->notification->image,

        ];
    }
}
