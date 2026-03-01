<?php

namespace App\Helpers;

class Cache {

    public static function flush() {
        cache()->store('custom')->clear();
    }

}
