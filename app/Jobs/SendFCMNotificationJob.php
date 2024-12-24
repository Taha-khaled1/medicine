<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
// $firebase = new FirebaseService();
// $firebase->sendNotification("cBFBj8SnTmmqAjSW3mLHn_:APA91bH0lWnS6IZjmJrJySOB5l2HVWSvmdS6hbWM-JdhCM1V8zwS1JFYGWjL9w0W7FZb2ytji6DXhoJoiVpLhqUkUmSz4Gv1hf1SizMO3qOK1QefrSm-YbLNLaoGT8M9ZZG5JROpIJMa", 'Test Title', 'Test Body');

class SendFCMNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fcmToken;
    protected $title;
    protected $message;
    protected $data;
    public function __construct($fcmToken, $title, $message, $data = [])
    {
        $this->fcmToken = $fcmToken;
        $this->title = $title;
        $this->data = $data;
        $this->message = $message;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $firebase = new FirebaseService();
        $firebase->sendNotification($this->fcmToken, $this->title, $this->message, $this->data);
    }
}
