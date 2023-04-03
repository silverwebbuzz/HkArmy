<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailTest extends Mailable
{
    use Queueable, SerializesModels;

    protected $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('krupavyas221@gmail.com', 'Scout')
                ->subject($this->details['subject'])
                ->to($this->details['email'])
                ->view($this->details['emailpage'])
                ->with('data',$this->details['data']);
        //return $this->view('emails.test')->with('name',$this->details['name'])->attach('G:\银色网络嗡嗡声测试.png');
    }
}
