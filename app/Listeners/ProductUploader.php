<?php

namespace App\Listeners;

use App\Events\ProductResizeImage;

class ProductUploader {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public $pathConfig;
    public function __construct() {
        //
         $this->pathConfig = config('image.path.product');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductProcessed  $event
     * @return void
     */
    public function handle(ProductResizeImage $event) {
        //
        if (isset($event->product['images'])) {
            $picture = $event->product['images'];
            if (count($picture) > 0) {
                foreach ($picture as $val) {
                    \App\Helpers\FileUpload::resizeImages($val);
                    \App\Helpers\FileUpload::deleteFiles($this->pathConfig['temp'], $val);
                }
            }
        }
        if (isset($event->product['image_dl'])) {
            $picture = $event->product['image_dl'];
            if (count($picture) > 0) {
                foreach ($picture as $val) {
                    if($val!=""){
                        \App\Helpers\FileUpload::moveTrash($val);
                    }
                    
                }
            }
        }
    }

}
