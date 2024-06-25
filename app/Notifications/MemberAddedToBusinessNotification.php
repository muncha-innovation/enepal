<?php

namespace App\Notifications;

use App\Mail\MemberAddedToBusinessMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberAddedToBusinessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $user;
    public $business;
    public $password;
    public $role;

    public function __construct($user, $business, $password, $role)
    {
        $this->user = $user;
        $this->business = $business;
        $this->password = $password;
        $this->role = $role;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->view('mail.member_added_to_business',[
            'business' => $this->business,
            'user' => $this->user,
            'password' => $this->password,
            'role' => $this->role,
        ]);
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
            'user_id' => $this->user->id,
            'business_id' => $this->business->id,
            'role' => $this->role,
            'created_by_id' => $this->business->id,
            'created_by_type' => 'business',
            'title' => 'Added to business',
            'message' => 'You have been added to '.$this->business->name,
        ];
    }
}
