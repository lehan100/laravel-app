<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
use App\Http\Resources\ProductCollection as ProductResource;
use App\Http\Requests\ProductPostRequest as mainRequest;
use App\Helpers\Template as Template;
use App\Helpers\Category as CategoryHelper;
use App\Helpers\Order\Timeline;
use App\Services\AdminFilter as AdminFilterService;
use App\Repositories\Product\ProductRepositoryInterface as RepositoryInterface;
use App\Repositories\Product\ProductOptionRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Store\AttributeSetRepositoryInterface;
use App\Repositories\Store\ProductOptionEntriesInterface;
use App\Repositories\Inventory\InventoryRepositoryInterface;

class ProductController extends MainController implements InterfaceController
{

    protected $controllerView = 'admin.pages.product.';
    protected $controllerName = 'product';
    protected $filterService;
    protected $timeline;
    protected $mainModel;
    protected $productOptionModel;
    protected $categoryModel;
    protected $attributeSetModel;
    protected $inventoryModel;
    protected $optionEntriesModel;

    public function __construct(
        RepositoryInterface $repository,
        ProductOptionRepositoryInterface $productOption,
        CategoryRepositoryInterface $categoryRepository,
        AdminFilterService $filterService,
        Timeline $timeline,
        AttributeSetRepositoryInterface $attributeSetRepository,
        InventoryRepositoryInterface $inventoryModel,
        ProductOptionEntriesInterface $optionEntriesModel,
    ) {
        $this->title = 'sản phẩm';
        $this->mainModel = $repository;
        $this->categoryModel = $categoryRepository;
        $this->attributeSetModel = $attributeSetRepository;
        $this->productOptionModel = $productOption;
        $this->inventoryModel = $inventoryModel;
        $this->optionEntriesModel = $optionEntriesModel;
        $this->timeline = $timeline;
        $sessionkey = $this->controllerName . "_filter";
        $this->filterService = $filterService;
        $this->filterService->setSessionKey($sessionkey);
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'filter' => $this->filterService, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request)
    {
        $this->metaTitle = 'Danh sách ' . $this->title;
        $itemsCategory = $this->categoryModel->listItems(['page' => [2]], ['task' => 'admin-list-items-selector']);
        $categorySellector = (new CategoryHelper())->Hierarchy($itemsCategory, false);

        $this->params['filter'] = $this->filterService->getData();
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);
        return view($this->controllerView . 'index', [
            'items' => $items,
            'categorySellector' => $categorySellector,
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
        $itemsCategory = $this->categoryModel->listItems(['page' => [2]], ['task' => 'admin-list-items-selector']);
        $categorySellector = (new CategoryHelper())->Hierarchy($itemsCategory, true);
        $listAttributeSet = $this->attributeSetModel->listItems(null, ['task' => 'admin-list-options']);
        if ($request->id !== null) {
            $params["id"] = $request->id;
            $item = $this->mainModel->getItem($params, ['task' => 'get-item']);
            //            echo '<pre>';
            //            print_r($item->toArray());die();
            $this->metaTitle = "Chỉnh sửa " . $this->title;
        }
        return view($this->controllerView . 'form', [
            'title' => $this->title,
            'buttomGroup' => $this->buttomGroup,
            'metaTitle' => $this->metaTitle,
            'item' => $item,
            'categorySellector' => $categorySellector,
            'listAttributeSet' => $listAttributeSet
        ]);
    }

    // @Override
    public function save(mainRequest $request)
    {
        if ($request->method() == 'POST') {
            $params = $request->all();
            $task = "add-item";
            $notify = "Thêm sản phẩm thành công!";
            $notify_error = "Thêm sản phẩm thất bại!";

            if ($params['id'] !== null) {
                $task = "edit-item";
                $notify = "Cập nhật sản phẩm thành công!";
                $notify_error = "Cập nhật sản phẩm thất bại!";
            }
            //            $a = json_decode($request->category_old,true);

            $id = $this->mainModel->saveItem($params, ['task' => $task]);
            if (!$id) {
                return redirect()->route($this->controllerName)->with("notify_error", $notify_error);
            }
            $params['product_id'] = $id;
            $params['task-main'] = $task;
            $status_option = $this->productOptionModel->saveItem($params);
            if (!$status_option && $id) {
                $notify_error = "Thêm thuộc tính thất bại!";
            }
            //inventory
            $getInventory = $this->inventoryModel->getItem(['product_id' => $params['product_id']], ['task' => 'get-item-by-product-id']);
            $available_quantity = ($getInventory) ? $getInventory['available_quantity'] : 0;

            if ($available_quantity != $params['quantity']) {
                $order_quantity = ($getInventory) ? $getInventory['order_quantity'] : 0;
                $sold_quantity = (int) $params['quantity'] - (int) $order_quantity;
                $inventory_id = $this->inventoryModel->saveItem(['product_id' => $params['product_id'], 'sold_quantity' => $sold_quantity, 'available_quantity' => $params['quantity']], ['task' => 'save-item']);
                $itemInventory = $this->inventoryModel->getItem(['id' => $inventory_id], ['task' => 'get-item']);
                $timeline = $itemInventory->timeline;
                if ($timeline) {
                    $dataTimeLine = json_decode($timeline->comments, true);
                    $this->timeline->setData($dataTimeLine);
                }
                if ($task == 'add-item') {
                    $this->timeline->createdTimeLine("Đã lưu số lượng tồn kho (" . $params['quantity'] . " sản phẩm)", auth()->user()->username);
                    $this->timeline->createdTimeLine("Đã lưu số lượng bán (" . $sold_quantity . " sản phẩm)", auth()->user()->username);
                }
                if ($task == 'edit-item') {
                    $this->timeline->createdTimeLine("Đã cập nhật số lượng tồn kho (" . $params['quantity'] . " sản phẩm)", auth()->user()->username);
                    $this->timeline->createdTimeLine("Đã cập nhật số lượng bán (" . $sold_quantity . " sản phẩm)", auth()->user()->username);
                }
                $dataTimeLine = ['inventory_id' => $inventory_id, 'data' => $this->timeline->getData()];
                $this->inventoryModel->updateTimeLine($dataTimeLine);
            }
            //inventorys
            if ($request->rollback == 1) {
                if (!$id || !$status_option)
                    return redirect()->route($this->controllerName . "/form", ['id' => $params['id']])->with("notify_error", $notify_error);
                else
                    return redirect()->route($this->controllerName . "/form", ['id' => $id])->with("notify", $notify);
            } else {
                if (!$id || !$status_option)
                    return redirect()->route($this->controllerName)->with("notify_error", $notify_error);
                else
                    return redirect()->route($this->controllerName)->with("notify", $notify);
            }
        }
    }

    // @Override
    public function delete(Request $request)
    {
        $status = FALSE;
        $message_success = config('configs.messages.delete_success');
        $message_error = config('configs.messages.delete_error');
        if (isset($request->id)) {
            $params["id"] = $request->id;
            $status = $this->mainModel->deleteItem($params, ['task' => 'delete-item']);
        }
        if ($status) {
            return redirect()->route($this->controllerName)->with('notify', $message_success);
        }
        return redirect()->route($this->controllerName)->with('notify_error', $message_error);
    }

    // @Override
    public function status(Request $request)
    {
        if (isset($request->id)) {
            $params["id"] = $request->id;
            $params["status"] = $request->status;
            $statusResult = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $xhtml = Template::showStatus($this->controllerName, $statusResult, $params["id"]);
            $status = $this->mainModel->saveItem($params, ['task' => 'change-status']);
            $response = array(
                'status' => $status,
                'xhtml' => $xhtml
            );
        } else {
            $response = array(
                'status' => false
            );
        }
        return json_encode($response);
    }

    // @Override
    public function multiple(Request $request)
    {
        $status = FALSE;
        $message_success = config('configs.messages.update_status_success');
        $message_error = config('configs.messages.update_status_error');
        switch ($request->type) {
            case 'active':
                $paramUpdate = 1;
                $status = $this->mainModel->saveItem(['aid' => $request->aid, 'value' => $paramUpdate], ['task' => 'admin-update-multi-status']);
                break;
            case 'inactive':
                $paramUpdate = 0;
                $status = $this->mainModel->saveItem(['aid' => $request->aid, 'value' => $paramUpdate], ['task' => 'admin-update-multi-status']);
                break;
            case 'delete':
                $status = $this->mainModel->deleteItem(['aid' => $request->aid], ['task' => 'delete-item-multi']);
                $message_success = config('configs.messages.delete_success');
                $message_error = config('configs.messages.delete_error');
                break;
        }

        if ($status) {
            return redirect()->route($this->controllerName)->with('notify', $message_success);
        }
        return redirect()->route($this->controllerName)->with('notify_error', $message_error);
    }

    public function filter(Request $request)
    {
        $this->filterService->deleteSession();
        $params = $request->all();
        if ($params['button'] == 'submit') {
            unset($params['_token']);
            unset($params['button']);
            foreach ($params as $key => $val) {
                if ($key == 'category_id' && $val != 0) {
                    $itemsCategory = $this->categoryModel->listItems(['page' => [2]], ['task' => 'admin-list-items-selector']);
                    $categorySellector = (new CategoryHelper())->generateDataId($itemsCategory, $val);
                    $val = $categorySellector;
                }
                if ($val != "") {
                    $this->filterService->setFilter($key, $val);
                }
            }
        }
        return redirect()->route($this->controllerName);
    }
    public function option(Request $request)
    {
        $index = $request->index;
        $view = view($this->controllerView . 'plugins/option', [
            'index' => $index,
        ])->render();
        return response()->json(['html' => $view]);
    }

    public function attribute(Request $request)
    {
        $index = $request->index;
        $type = $request->type;
        $view = view($this->controllerView . 'plugins/option_attribute', [
            'index' => $index,
            'type' => $type,
        ])->render();
        return response()->json(['html' => $view]);
    }

    public function value(Request $request)
    {
        $index = $request->index;
        $type = $request->type;
        $view = view($this->controllerView . 'plugins/option_value', [
            'index' => $index,
            'type' => $type,
        ])->render();
        return response()->json(['html' => $view]);
    }
    // Select
    public function productSelect(Request $request)
    {
        try {
            $checked = $request->selected;
            if ($checked != "") {
                $checked = explode(",", $checked);
            }
            $this->params['ids'] = $checked;
            $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items-selector']);
            foreach ($items->toArray() as $item) {
                $data[] = new ProductResource($item);
            }
            return response()->json(['data' => $data, 'status' => true], 200);
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'message' => 'Get Data Error'], 200);
        }
    }

    public function optionEntries(Request $request)
    {
        try {
            //code...
            $listItems = $this->optionEntriesModel->listItems($this->params, ['task' => 'admin-list-options']);
            return response()->json(['status' => true, 'data' => $listItems, 'message' => 'Get Data Success'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'message' => 'Get Data Error'], 200);
        }
    }
}
