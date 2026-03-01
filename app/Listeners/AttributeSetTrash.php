<?php

namespace App\Listeners;
use App\Events\AttributeSetTrashImage;
class AttributeSetTrash {

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
     * @param  \App\Events\ProductTrashImageOption  $event
     * @return void
     */
    public function handle(AttributeSetTrashImage $event) {
        if (isset($event->attributes)) {
            $attributes = $event->attributes;
             $pathConfig = $this->pathConfig;
            if (count($attributes) > 0) {
                foreach ($attributes as $item) {
                    $picture = $item->picture;
                    if ($picture != "") {
//                        \App\Helpers\FileUpload::moveTrash($picture);
                        \App\Helpers\FileUpload::moveTrashImageProccess($picture, $pathConfig['path'], $pathConfig['trash']);
                    }
                }
            }
        }
    }

}
