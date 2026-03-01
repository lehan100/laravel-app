<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Repositories\Media\PhotoEloquentRepository;
use App\Repositories\Category\CategoryEloquentRepository;
use App\Models\Storage;
use App\Helpers\Head;
use Illuminate\Support\Facades\Storage as StorageDisk;
class FrontendController extends Controller {

    public $params = ['pagination' => [
            'totalItemsPerPage' => 20,
            'pageLimit' => 6
    ]];
    public $head;
    protected $shoppingCart;
    public $configPath;
    public function __construct() {
        if( Route::current()->parameter("type")=='ajax'){
            
        }
        $configPath = config('image.path');
        $this->configPath = $configPath;
        $this->head = new Head();
        $photoModel = new PhotoEloquentRepository();
        $categoryModel = new CategoryEloquentRepository();
        $logo = $photoModel->getItem(['code' => 'logo'], ['task' => 'frontend-get-item']);
        $favicon = $photoModel->getItem(['code' => 'favicon'], ['task' => 'frontend-get-item']);
        //Settings
        if (StorageDisk::disk('main')->exists("settings.json")) {
            $data = StorageDisk::disk('main')->get('settings.json');
            $settings = json_decode($data, true);
        } else {
            $dataConfig = (new Storage)->getStorage("settings");
            if ($dataConfig) {
                $settings = json_decode($dataConfig->data, true);
                StorageDisk::disk('main')->put('settings.json', json_encode($settings));
            }
        }
        $locals = [];
        if (StorageDisk::disk('main')->exists("locals.json")) {
            $data = StorageDisk::disk('main')->get('locals.json');
            $locals = json_decode($data, true);
        }else{
            $dataLocals = (new Storage)->getStorage("locals");
            if($dataLocals){
                $items = json_decode($dataLocals->data,true);
                StorageDisk::disk('main')->put('locals.json', json_encode($items));
            }
        }
        //Settings
        //Category
        $itemsCategoryFooterA = $categoryModel->listItems(['position' => 'position_footer_a'], ['task' => "frontend-list-items-positions"]);
        $itemsCategoryFooterB = $categoryModel->listItems(['position' => 'position_footer_b'], ['task' => "frontend-list-items-positions"]);
        //Category
        $this->head->headMeta($settings['title'], 'title');
        $this->head->headMeta($settings['keyword'], 'keywords');
        $this->head->headMeta($settings['description'], 'description');
       
        $this->head->headMetaProperty($settings['title'], 'og:title');
        $this->head->headMetaProperty($settings['keyword'], 'og:keywords');
        $this->head->headMetaProperty($settings['description'], 'og:description');
        if ($logo) {
            $image_src = asset($configPath['photo']['path'] . "/" . $logo->picture);
            $this->head->headMetaProperty($image_src, 'og:image');
        }

        $this->head->headLink(['rel' => 'canonical', 'link' => url('')]);
        $this->head->headMeta("1 days", "revisit-after");
        $this->head->headMeta(url(''), "GENERATOR");
        if ($favicon) {
            $this->head->headLink(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => asset("media/photo/" . $favicon['picture'])]);
            $this->head->headLink(['rel' => 'SHORTCUT ICON', 'type' => 'image/x-icon', 'href' => asset("media/photo/" . $favicon['picture'])]);
        }


        view()->share([
            'configPath' => $configPath,
            'logo' => $logo,
            'head' => $this->head,
            'settings' => $settings,
            'locals' => $locals,
            'blockFooterOne'=>$itemsCategoryFooterA,
            'blockFooterTwo'=>$itemsCategoryFooterB,
        ]);
    }

}
