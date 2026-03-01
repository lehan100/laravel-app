<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Scheduling\Schedule;

class CacheController extends MainController
{

    protected $controllerView = 'admin.pages.cache.';
    protected $controllerName = 'cache';
    protected $schedule;

    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
        $this->title = 'Cache Management';
        //$this->mainModel = new mainModel();
        view()->share(['mainTitle' => $this->title, 'params' => $this->params,  'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    public function index(Request $request)
    {
        $this->metaTitle = 'Cache Management';
        //query

        return view($this->controllerView . 'index', [
            'title' => $this->title,
            'metaTitle' => $this->metaTitle
        ]);
    }
    public function refresh(Request $request)
    {
        try {
            $type = $request->type;
			$notify = "Refresh cache successfully!";
            switch ($type) {
                case 'cache':
                    $notify = "Refresh files cache successfully!";
                    Artisan::call('cache:clear-custom');
                    break;
                case 'route':
                    $notify = "Refresh route cache successfully!";
                    //Artisan::call('route:clear');
                    Artisan::call('route:cache');
                    break;
                case 'config':
                    $notify = "Refresh config cache successfully!";
                    // Artisan::call('config:clear');
                    Artisan::call('config:cache');
                    break;
                case 'view':
                    $notify = "Refresh view cache successfully!";
                    // Artisan::call('view:clear');
                    Artisan::call('view:cache');
                case 'event':
                    $notify = "Refresh event cache successfully!";
                    // Artisan::call('view:clear');
                    Artisan::call('event:cache');
                    break;
                case 'all':
                    # code...
					$notify = "Refresh all cache successfully!";
                    // Artisan::call('cache:clear');
					Artisan::call('cache:clear-custom');
                    Artisan::call('route:cache');
                    Artisan::call('config:cache');
                    Artisan::call('view:cache');
                    Artisan::call('event:cache');
                    break;
            }
            return redirect()->route($this->controllerName)->with("notify",  $notify);
        } catch (\Throwable $th) {
            $notify = "Refresh cache error!";
            return redirect()->route($this->controllerName)->with("notify_error",  $notify);
        }
    }
    public function flush(Request $request)
    {
        try {
            \App\Helpers\Cache::flush();
            $notify = "Refresh database cache successfully!";
            return back()->with($this->controllerName)->with("notify",  $notify);
        } catch (\Throwable $th) {
            $notify = "Refresh database error!";
            return redirect()->route($this->controllerName)->with("notify_error",  $notify);
        }
    }
    public function image(Request $request)
    {
        try {
            Artisan::call('media:clear-image-var');
            $notify = "Clear temp image successfully!";
            return back()->with($this->controllerName)->with("notify",  $notify);
        } catch (\Throwable $th) {
            $notify = "Clear temp image error!";
            return redirect()->route($this->controllerName)->with("notify_error",  $notify);
        }
    }
    public function permission(Request $request)
    {
        try {
            Artisan::call('permission:create-permission-routes');
            $notify = "Permission routes added successfully!";
            return back()->with($this->controllerName)->with("notify",  $notify);
        } catch (\Throwable $th) {
            $notify = "Permission routes added error.!";
            return redirect()->route($this->controllerName)->with("notify_error",  $notify);
        }
    }
}
