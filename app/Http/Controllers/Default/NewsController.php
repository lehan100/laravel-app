<?php

namespace App\Http\Controllers\Default;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Helpers\Category as CategoryHelper;
use App\Helpers\Seo as SEO;
use App\Http\Controllers\FrontendController;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Services\ProductHit;

class NewsController extends FrontendController
{

    protected $controllerView = 'default.pages.news.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'news';
    protected $postModel;
    protected $categoryModel;
    protected $SEO;
    protected $HIT;
    public function __construct(
        PostRepositoryInterface $postModel,
        CategoryRepositoryInterface $categoryModel,
        ProductHit $HIT,
    ) {
        parent::__construct();
        $this->categoryModel = $categoryModel;
        $this->postModel = $postModel;
        $this->SEO = new SEO($this->head);
        $this->HIT = $HIT;
        $this->HIT->setSessionKey("news_hit");
        view()->share(['params' => $this->params]);
    }
    public function list(Request $request)
    {
        // $this->params['pagination']['totalItemsPerPage'] = 6;
        if ($request->id !== null) {
            $id_category = $request->id;
            $this->params['id_category'] = $id_category;
            $this->params['page'] = $request->page;
            $itemsCategory = $this->categoryModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
            $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $id_category);
            if (!$itemsBreadcrumbs) {
                abort(404);
            }
            $category_ids = (new CategoryHelper)->generateDataId($itemsCategory, $id_category, false);
            if (count($category_ids) <= 0) {
                $category_ids = [
                    0 => $id_category
                ];
            }
            $this->params['id_categories'] = $category_ids;
            $listCategorySiteBar = $this->categoryModel->listItems($this->params, ['task' => 'frontend-list-items-by-id']);
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
            $newsItems = $this->postModel->listItems($this->params, ['task' => 'frontend-list-items']);

            //    echo '<pre>'; print_r($newsItems->toArray());die();
            //Get Data    
            if (isset($request->lazyload) && $request->lazyload == true) {
                $dataLoading = view($this->controllerView . 'ajax/list', [
                    'newsItems' => $newsItems,
                ])->render();
                $htmlPagination = view($this->controllerView . 'ajax/pagination', [
                    'listItems' => $newsItems
                ])->render();
                return response()->json([
                    'status' => true,
                    'lists' => \App\Helpers\Content::minifyContent($dataLoading),
                    'pagination' => \App\Helpers\Content::minifyContent($htmlPagination)
                ]);
            }
            $newsItemsTopViews = $this->postModel->listItems($this->params, ['task' => 'frontend-list-items-top-view']);
            // print_r($newsItemsTopViews->toArray());die();
            return view($this->controllerView . 'list', [
                'breadcrumbs' => $breadcrumbs,
                'category_id' => $id_category,
                'menuSiteBar' => $listCategorySiteBar,
                'newsItems' => $newsItems,
                'postViewer' => $newsItemsTopViews

            ]);
        }
        abort(404);
    }
    public function view(Request $request)
    {
        // try {
        if ($request->id !== null) {
            $this->params['id'] = $request->id;
            $item = $this->postModel->getItem($this->params, ['task' => 'frontend-get-item']);
            if (!$item) {
                abort(404);
            }

            $this->params['category_id'] = $item->category_id;

            $this->params['id_categories'] = [$item->category_id];
            $itemsCategory = $this->categoryModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
            $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $item->category_id);
            $category_ids = (new CategoryHelper)->generateDataId($itemsCategory, $item->category_id, false);
            $categody_ids_parents = (new CategoryHelper)->getDataIDBreadcrumb($itemsBreadcrumbs);
            if (!is_null($categody_ids_parents)) {
                $category_ids = array_merge($category_ids, $categody_ids_parents);
            }
            $this->params['id_categories'] = $category_ids;
            $picture = $item->picture;
            $image_src = "";
            if ($picture != "") {
                $image_src = asset($this->configPath['post']['path'] . "/" . $picture);
                $this->SEO->setMetaProperty('og:image', $image_src);
            }

            $this->SEO->metaTags($item);
            //Breadcrumbs
            $breadcrumbs = view($this->controllerViewLayout . 'elements/breadcrumb_home', [
                'itemsBreadcrumbs' => $itemsBreadcrumbs,
            ])->render();
            //end Breadcrumbs
            //Get Data
            $newsItemsTopViews = $this->postModel->listItems($this->params, ['task' => 'frontend-list-items-top-view']);
            //Get Data
            return view($this->controllerView . 'view', [
                'breadcrumbs' => $breadcrumbs,
                'postItem' => $item,
                'postViewer' => $newsItemsTopViews
            ]);
        }
        // } catch (\Throwable $th) {
        //     abort(404);
        // }
    }
    public function viewer(Request $request)
    {
        if ($request->id !== null) {
            if ($this->HIT->setViewer($request->id)) {
                $this->postModel->saveItem(['id' => $request->id], ['task' => "update-hit"]);
                return response()->json(['status' => true]);
            }
        }
        return response()->json(['status' => false]);
    }
    public function detail(Request $request)
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
                foreach ($itemsBreadcrumbs as $val) {
                    $title[] = $val->name;
                }
                $title = Arr::join(array_reverse($title), ' - ');
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
            return view($this->controllerView . 'detail', [
                'breadcrumbs' => $breadcrumbs,
                'newItem' => $newItem,
                'sortCategory' => $sortCategory
            ]);
        }
        abort(404);
    }
}
