<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class MediaResizeImage {

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;
    public $media;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($media) {
        //
        $this->media = $media;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }

//    public function boot() {
//        Event::listen(
//                ProductProcessed::class,
//                [ProductUploader::class, 'handle']
//        );
//
//        Event::listen(function (ProductProcessed $event) {
//            //
//            echo '1333';
//            die("111");
//        });
//    }

}
