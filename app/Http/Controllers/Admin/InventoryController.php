<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Helpers\Template as Template;
use App\Repositories\Inventory\InventoryRepositoryInterface as RepositoryInterface;
use App\Services\AdminFilter;
use App\Helpers\Order\Timeline;

class InventoryController extends MainController
{

    protected $controllerView = 'admin.pages.inventory.';
    protected $controllerName = 'inventory';
    protected $mainModel;
    protected $filterService;
    protected $timeline;
    public function __construct(RepositoryInterface $repository, AdminFilter $filterService, Timeline $timeline)
    {
        $this->mainModel = $repository;
        $this->timeline = $timeline;
        $sessionkey = $this->controllerName . "_filter";
        $this->filterService = $filterService;
        $this->filterService->setSessionKey($sessionkey);
        $this->title = 'Quản lý tồn kho';
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'filter' => $this->filterService, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request)
    {
        $this->metaTitle =  $this->title;

        $this->params['filter'] = $this->filterService->getData();
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);

        return view($this->controllerView . 'index', [
            'items' => $items,
            'title' => $this->title,
            'metaTitle' => $this->metaTitle,
            'filter' => $this->params['filter']
        ]);
    }

    // @Override
    public function form(Request $request)
    {
        $this->metaTitle = "Thêm " . $this->title;
        $this->buttomGroup = "form";
        $this->title = "Thông tin";
        $item = null;
        if ($request->id !== null) {
            $params["id"] = $request->id;
            $item = $this->mainModel->getItem($params, ['task' => 'get-item']);

            $this->metaTitle = "Chỉnh sửa " . $this->title;
        }
        return view($this->controllerView . 'form', [
            'title' => $this->title,
            'buttomGroup' => $this->buttomGroup,
            'metaTitle' => $this->metaTitle,
            'item' => $item,
        ]);
    }

    // @Override
    public function save(Request $request)
    {
        if ($request->method() == 'POST') {
            $params = $request->all();

            $task = "save-item";
            $getInventory = $this->mainModel->getItem(['product_id' => $params['product_id']], ['task' => 'get-item-by-product-id']);
            if ($this->mainModel->saveItem($params, ['task' => $task])) {
                $inventory_id = $params['id'];
                $available_quantity = ($getInventory) ? $getInventory['available_quantity'] : 0;
                if ($available_quantity != $params['available_quantity']) {
                    $itemInventory = $this->mainModel->getItem(['id' => $inventory_id], ['task' => 'get-item']);
                    $timeline = $itemInventory->timeline;
                    if ($timeline) {
                        $dataTimeLine = json_decode($timeline->comments, true);
                        $this->timeline->setData($dataTimeLine);
                    }
                    $this->timeline->createdTimeLine("Đã cập nhật số lượng tồn kho (" . $params['available_quantity'] . " sản phẩm)", auth()->user()->username);
                    $this->timeline->createdTimeLine("Đã cập nhật số lượng bán (" . $params['sold_quantity'] . " sản phẩm)", auth()->user()->username);
                    $dataTimeLine = ['inventory_id' => $inventory_id, 'data' => $this->timeline->getData()];
                    $this->mainModel->updateTimeLine($dataTimeLine);
                }
                return response()->json(['status' => true]);
            }
        }
        return response()->json(['status' => false]);
    }

    public function history(Request $request)
    {
        if (isset($request->id)) {
            try {
                //code...
                $id = $request->id;
                $itemInventory = $this->mainModel->getItem(['id' => $id], ['task' => 'get-item']);
                $timeline = $itemInventory->timeline;
                $dataTimeLine = json_decode($timeline->comments, true);
                foreach ($dataTimeLine as $k => $item) {
                    $createdAt = \Illuminate\Support\Carbon::parse($item['date']);
                    $created_at = $createdAt->format('d/m/Y h:m:s');
                    $dataTimeLine[$k]['date'] = $created_at;
                }
                $product = $itemInventory->product;
                $image = (new \App\Helpers\Product\Image())->getLinkDefault($product, 'small');
                $product['picture'] = $image;
                return response()->json(['status' => true, 'data' => ['product' => $itemInventory->product, 'timeline' => $dataTimeLine]]);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['status' => false]);
            }
        }
        return response()->json(['status' => false]);
    }
    public function filter(Request $request)
    {
        $this->filterService->deleteSession();
        $params = $request->all();
        if ($params['button'] == 'submit') {
            unset($params['_token']);
            unset($params['button']);
            foreach ($params as $key => $val) {
                if ($val != "") {
                    $this->filterService->setFilter($key, $val);
                }
            }
        }
        return redirect()->route($this->controllerName);
    }
}
