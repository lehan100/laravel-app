<?php

namespace App\Http\Controllers\Default;
use App\Helpers\Category as CategoryHelper;
use App\Http\Controllers\FrontendController;
use App\Repositories\Media\PhotoRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Sales\SalesRepositoryInterface;
use App\Jobs\SendNewOrderMail;
use \Illuminate\Support\Carbon;
//use App\Helpers\Snippet;
//use function Ramsey\Uuid\v1;

class HomeController extends FrontendController {

    protected $controllerView = 'default.pages.home.';
    protected $controllerName = 'home';
    protected $photoModel;
    protected $categoryModel;
    protected $productModel;
    protected $salesModel;

    public function __construct(
        PhotoRepositoryInterface $photoModel, 
        CategoryRepositoryInterface $categoryModel, 
        ProductRepositoryInterface $productModel,
        SalesRepositoryInterface $salesModel,
        ) {
        parent::__construct();
        $this->photoModel = $photoModel;
        $this->categoryModel = $categoryModel;
        $this->productModel = $productModel;
        $this->salesModel = $salesModel;
    }

    // @Override
    public function index() {
        $sliders = $this->photoModel->listItems(['code' => 'slider'], ['task' => 'frontend-list-items']);
        $blockAds = $this->photoModel->listItems(['code' => 'block-left'], ['task' => 'frontend-list-items']);
        $itemsCategory = $this->categoryModel->listItems(['page' => [2]], ['task' => 'admin-list-items-selector']);
        $itemsCategoryTop = $this->categoryModel->listItems(['position' => 'position_top'], ['task' => "frontend-list-items-positions"]);
        $itemsCategoryCenter = $this->categoryModel->listItems(['position' => 'position_main', 'itemsCategory' => $itemsCategory], ['task' => "frontend-list-items-positions"]);
        $listSales = $this->salesModel->listItems([],['task'=>'fronend-list-items']);
        foreach ($itemsCategoryCenter as $val) {
            $category_id = (new CategoryHelper)->generateDataId($itemsCategory,$val->id);
            $itemsProducts[$val->id] = $this->productModel->listItems(['category_id'=>$category_id], ['task' => "frontend-main-list-items-limit"]);
        }
		//$snippet = new Snippet();
		//$data = $snippet ->searchSchema()->getSchemaData();
        
       // echo "<pre>";echo json_encode($data);die();
        //  $emailJob = (new SendNewOrderMail($sliders))->delay(Carbon::now()->addMinutes(1));
        //  dispatch($emailJob);
        //return view("mail.new_order");
        return view($this->controllerView . 'index', [
            'sliders' => $sliders,
            'blockAds' => $blockAds,
            'itemsCategoryTop' => $itemsCategoryTop,
            'itemsCategoryCenter' => $itemsCategoryCenter,
            'itemsProducts'=>$itemsProducts,
            'productModel'=>$this->productModel,
            'listSales'=>$listSales
        ]);
    }

}
