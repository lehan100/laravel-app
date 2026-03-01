<?php

namespace App\Listeners;

use App\Events\CategoryResizeImage;

class CategoryUploader {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;

    public function __construct() {
        //
        $this->pathConfig = config('image.path.category');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductProcessed  $event
     * @return void
     */
    public function handle(CategoryResizeImage $event) {
        //
        $pathConfig = $this->pathConfig;
        if (isset($event->category['image']) && $event->category['image'] != "") {
            $picture = $event->category['image'];
            \App\Helpers\FileUpload::resizeImagesProccess($picture, $pathConfig['temp'], $pathConfig['path'], $pathConfig['size']);
            \App\Helpers\FileUpload::deleteFiles('var/temp', $picture);
        }
        if (isset($event->category['image_dl']) && $event->category['image_dl'] != "") {
            $picture = $event->category['image_dl'];
            \App\Helpers\FileUpload::moveTrashImageProccess($picture, $pathConfig['path'], $pathConfig['trash']);
        }
    }

}
