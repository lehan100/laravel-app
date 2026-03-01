<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider {

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\ProductResizeImage::class => [
            \App\Listeners\ProductUploader::class,
        ],
        \App\Events\ProductResizeImageOption::class => [
            \App\Listeners\ProductOptionUploader::class,
        ],
        \App\Events\ProductTrashImageOption::class => [
            \App\Listeners\ProductOptionTrash::class,
        ],
        \App\Events\PostResizeImage::class => [
            \App\Listeners\PostUploader::class,
        ],
        \App\Events\MediaResizeImage::class => [
            \App\Listeners\MediaUploader::class,
        ],
        \App\Events\CategoryResizeImage::class => [
            \App\Listeners\CategoryUploader::class,
        ],
        \App\Events\AttributeSetResizeImage::class => [
            \App\Listeners\AttributeSetUploader::class,
        ],
        \App\Events\AttributeSetTrashImage::class => [
            \App\Listeners\AttributeSetTrash::class,
        ],
        \App\Events\RatingResizeImage::class => [
            \App\Listeners\RatingUploader::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents() {
        return false;
    }

}
