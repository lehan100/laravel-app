<?php

namespace App\Listeners;

use App\Events\ProductResizeImageOption;

class ProductOptionUploader {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;
    public function __construct() {
        //
        $this->pathConfig = config('image.path.product_option');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductResizeImageOption  $event
     * @return void
     */
    public function handle(ProductResizeImageOption $event) {
        if (isset($event->picture)) {
            $picture = $event->picture;
            if ($picture != "") {
                \App\Helpers\FileUpload::resizeImageOption($picture);
                \App\Helpers\FileUpload::deleteFiles($this->pathConfig['temp'], $picture);
            }
        }
    }

}
