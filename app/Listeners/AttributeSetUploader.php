<?php

namespace App\Listeners;

use App\Events\AttributeSetResizeImage;

class AttributeSetUploader {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;

    public function __construct() {
        //
        $this->pathConfig = config('image.path.attribute_set');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductResizeImageOption  $event
     * @return void
     */
    public function handle(AttributeSetResizeImage $event) {
        $pathConfig = $this->pathConfig;
        if (isset($event->picture)) {
            $picture = $event->picture;
            if ($picture != "") {
                \App\Helpers\FileUpload::resizeImageAttribute($picture);
                \App\Helpers\FileUpload::deleteFiles($this->pathConfig['temp'], $picture);
            }
        }
    }

}
