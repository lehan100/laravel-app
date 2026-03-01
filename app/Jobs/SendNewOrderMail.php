<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrder;
class SendNewOrderMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $data;
    public function __construct($data)
    {
        //
        $this->data = $data;
       
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $email = new NewOrder($this->data);
		$mailcustomer = $this->data['customer']['email'];
        Mail::to($mailcustomer)->send($email);
        Mail::to('lehan100@gmail.com')->send($email);
    }
}
