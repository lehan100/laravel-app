<?php

namespace App\Listeners;

use App\Events\RatingResizeImage;

class RatingUploader
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;

    public function __construct()
    {
        //
        $this->pathConfig = config('image.path.rating');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductProcessed  $event
     * @return void
     */
    public function handle(RatingResizeImage $event)
    {
        //
        $pathConfig = $this->pathConfig;
        if (isset($event->images) && $event->images != "") {
            $pictures = explode(",", $event->images);
            foreach ($pictures as $picture) {
                \App\Helpers\FileUpload::photoProccess($picture, $pathConfig['temp'], $pathConfig['path'], $pathConfig['size'], $pathConfig['thumb']);
                \App\Helpers\FileUpload::deleteFiles('var/temp', $picture);
            }
        }
    }
}
