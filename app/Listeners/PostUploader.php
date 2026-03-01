<?php

namespace App\Listeners;

use App\Events\PostResizeImage;

class PostUploader {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;

    public function __construct() {
        //
        $this->pathConfig = config('image.path.post');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductProcessed  $event
     * @return void
     */
    public function handle(PostResizeImage $event) {
        //
        $pathConfig = $this->pathConfig;
        if (isset($event->post['image']) && $event->post['image'] != "") {
            $picture = $event->post['image'];
            \App\Helpers\FileUpload::resizeImagesProccess($picture, $pathConfig['temp'], $pathConfig['path'], $pathConfig['size'], $pathConfig['thumb']);
            \App\Helpers\FileUpload::deleteFiles('var/temp', $picture);
        }
        if (isset($event->post['image_dl']) && $event->post['image_dl'] != "") {
            $picture = $event->post['image_dl'];
            \App\Helpers\FileUpload::moveTrashImageProccess($picture, $pathConfig['path'], $pathConfig['trash']);
        }
    }

}
