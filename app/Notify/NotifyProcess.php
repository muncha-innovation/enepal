<?php

namespace App\Notify;

use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyProcess
{
    protected $template;
    protected $user;
    protected $shortCodes = [];
    protected $notificationTemplate;
    
    /**
     * Set the notification template by action
     */
    public function setTemplate($action)
    {
        $this->notificationTemplate = NotificationTemplate::where('action', $action)->first();
        
        if (!$this->notificationTemplate) {
            throw new \Exception("Notification template not found for action: {$action}");
        }
        
        return $this;
    }
    
    /**
     * Set the user who will receive the notification
     */
    public function setUser($user)
    {
        $this->user = $user;
        
        // Set default shortcodes
        $this->shortCodes = [
            'fullname' => $user->fullname,
            'site_name' => config('app.name'),
            'support_email' => config('mail.from.address')
        ];
        
        return $this;
    }
    
    /**
     * Add additional shortcodes if needed
     */
    public function withShortCodes(array $shortCodes)
    {
        $this->shortCodes = array_merge($this->shortCodes, $shortCodes);
        return $this;
    }
    
    /**
     * Send the notification
     */
    public function send()
    {
        if (!$this->user || !$this->notificationTemplate) {
            return false;
        }

        $success = true;

        // Send Email if enabled
        if ($this->notificationTemplate->email_status) {
            $success &= $this->sendEmail();
        }

        // Send SMS if enabled
        if ($this->notificationTemplate->sms_status) {
            $success &= $this->sendSMS();
        }

        // Send Push if enabled
        if ($this->notificationTemplate->push_status) {
            $success &= $this->sendPush();
        }

        return $success;
    }

    /**
     * Send email notification
     */
    protected function sendEmail()
    {
        $template = $this->notificationTemplate;
        
        // Get user's preferred locale, fallback to default
        $locale = $this->user->preferred_locale ?? config('app.locale');
        
        $subject = $template->getTranslation('subject', $locale);
        $body = $template->getTranslation('email_body', $locale);
        
        // Replace shortcodes
        $body = $this->replaceShortCodes($body);
        $subject = $this->replaceShortCodes($subject);
        
        // Your existing email sending logic here
        // Example using Laravel's Mail facade:
        try {
            Mail::send([], [], function ($message) use ($subject, $body) {
                $message->to($this->user->email, $this->user->fullname)
                    ->subject($subject)
                    ->from(
                        $this->notificationTemplate->email_sent_from_email,
                        $this->notificationTemplate->email_sent_from_name
                    )
                    ->setBody($body, 'text/html');
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS notification
     */
    protected function sendSMS()
    {
        $template = $this->notificationTemplate;
        $locale = $this->user->preferred_locale ?? config('app.locale');
        $body = $template->getTranslation('sms_body', $locale);
        $body = $this->replaceShortCodes($body);
        
        // TODO: Your SMS sending logic here
        return true;
    }

    /**
     * Send Push notification
     */
    protected function sendPush()
    {
        $template = $this->notificationTemplate;
        $locale = $this->user->preferred_locale ?? config('app.locale');
        
        $title = $template->getTranslation('push_title', $locale);
        $body = $template->getTranslation('push_body', $locale);
        
        $title = $this->replaceShortCodes($title);
        $body = $this->replaceShortCodes($body);
        
        // TODO:Your Push notification logic here
        return true;
    }

    /**
     * Replace shortcodes in content
     */
    protected function replaceShortCodes($content)
    {
        foreach ($this->shortCodes as $code => $value) {
            $content = str_replace('{{' . $code . '}}', $value, $content);
        }
        
        return $content;
    }
}
