<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;

class AdminContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $messageData;

    public function __construct(ContactMessage $messageData)
    {
        $this->messageData = $messageData;
    }

    public function build()
    {
        return $this->subject('New Contact Message Received')
                    ->view('emails.admin_contact_notification')
                    ->with([
                        'name' => $this->messageData->name,
                        'email' => $this->messageData->email,
                        'messageContent' => $this->messageData->message,
                    ]);
    }
}
