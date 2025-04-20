<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPostNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function build()
    {
        return $this->subject('New Post Published')
                    ->view('emails.new_post')
                    ->with([
                        'title' => $this->post['title'],
                        'description' => $this->post['description'],
                        'author' => $this->post['author'],
                    ]);
    }
}
