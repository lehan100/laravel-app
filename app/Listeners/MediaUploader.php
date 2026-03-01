<?php

namespace App\Listeners;

use App\Events\MediaResizeImage;

class MediaUploader {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;

    public function __construct() {
        //
        $this->pathConfig = config('image.path.photo');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductProcessed  $event
     * @return void
     */
    public function handle(MediaResizeImage $event) {
        //
//        echo "<pre>";
//        print_r($event);die();
        $pathConfig = $this->pathConfig;
        if (isset($event->media['image']) && $event->media['image'] != "") {
            $picture = $event->media['image'];
            \App\Helpers\FileUpload::photoProccess($picture, $pathConfig['temp'], $pathConfig['path'], $pathConfig['size']);
            \App\Helpers\FileUpload::deleteFiles('var/temp', $picture);
        }
        if (isset($event->media['image_dl']) && $event->media['image_dl'] != "") {
            $picture = $event->media['image_dl'];
            \App\Helpers\FileUpload::moveTrashImageProccess($picture, $pathConfig['path'], $pathConfig['trash']);
        }
    }

}
