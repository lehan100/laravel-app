<?php

namespace App\Listeners;
use App\Events\ProductTrashImageOption;
class ProductOptionTrash {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductTrashImageOption  $event
     * @return void
     */
    public function handle(ProductTrashImageOption $event) {
        if (isset($event->attributes)) {
            $attributes = $event->attributes;
            if (count($attributes) > 0) {
                foreach ($attributes as $item) {
                    $picture = $item->picture;
                    if ($picture != "") {
                       \App\Helpers\FileUpload::moveTrash($picture);
                    }
                }
            }
        }
    }

}
