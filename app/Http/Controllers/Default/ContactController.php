<?php

namespace App\Http\Controllers\Default;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Requests\Default\ContactPostRequest;
use App\Helpers\Category as CategoryHelper;
use App\Helpers\Seo as SEO;
use App\Http\Controllers\FrontendController;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\Contact\ContactRepositoryInterface;
use App\Jobs\SendEmailContact;
use \Illuminate\Support\Carbon;
class ContactController extends FrontendController
{

    protected $controllerView = 'default.pages.contact.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'contact';
    protected $categoryModel;
    protected $postModel;
    protected $contactModel;
    protected $SEO;
    public function __construct(
        CategoryRepositoryInterface $categoryModel,
        PostRepositoryInterface $postModel,
        ContactRepositoryInterface $contactModel
    ) {
        parent::__construct();
        $this->categoryModel = $categoryModel;
        $this->postModel = $postModel;
        $this->contactModel = $contactModel;
        $this->SEO = new SEO($this->head);
    }
    public function index(Request $request)
    {
        if ($request->id !== null) {
            $id_category = $request->id;
            $this->params['id_category'] = $id_category;
            $itemsCategory = $this->categoryModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
            $listCategorySiteBar = $this->categoryModel->listItems(['page' => [1, 4]], ['task' => 'frontend-list-items-breadcrumbs']);
            $sortCategory = (new CategoryHelper())->generateNavigationMenu($listCategorySiteBar, 'menuSiteBar', $id_category);
            $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $id_category);
            if (!$itemsBreadcrumbs) {
                abort(404);
            }

            // META
            $categoryDetail = $this->categoryModel->getItem($this->params, ['task' => 'frontend-get-item']);
            $picture = $categoryDetail->picture;
            $image_src = "";
            if ($picture != "") {
                $image_src = asset($this->configPath['category']['path'] . "/" . $picture);
                $this->SEO->setMetaProperty('og:image', $image_src);
            }
            if ($categoryDetail->contents->title == "") {
                $array  = Arr::map($itemsBreadcrumbs,function($item){
                    return $item->name;
                });
                $title = Arr::join(array_reverse($array), ' - ');
                $categoryDetail->contents->title = $title;
            }
            $this->SEO->metaTags($categoryDetail);
            // end META
            //Breadcrumbs
            $breadcrumbs = view($this->controllerViewLayout . 'elements/breadcrumb_home', [
                'itemsBreadcrumbs' => $itemsBreadcrumbs,
            ])->render();
            //end Breadcrumbs
            //Get Data
            $newItem = $this->postModel->getItem($this->params, ['task' => 'frontend-get-item-w-category']);
            //Get Data
            return view($this->controllerView . 'index', [
                'breadcrumbs' => $breadcrumbs,
                'sortCategory' => $sortCategory,
                'newItem' => $newItem,
                'item' => $categoryDetail
            ]);
        }
        abort(404);
    }

    public function post(ContactPostRequest $request)
    {
        try {
            //code...
            $this->params = $request->all();
            $status = $this->contactModel->saveItem($this->params, ['task' => 'add-item']);
            // Send Mail
            $emailJob = (new SendEmailContact($request->all()))->delay(Carbon::now()->addMinutes(1));
            dispatch($emailJob);
            // End Send Mail
            return response()->json(['status' =>  $status,'code'=>'200']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false]);
        }
    }
}
