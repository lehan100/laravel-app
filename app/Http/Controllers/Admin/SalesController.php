<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Arr;
use App\Http\Resources\ProductCollection as ProductResource;
use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
use App\Http\Requests\SalesPostRequest as mainRequest;
use App\Helpers\Template as Template;
//use App\Models\Province as mainModel;
use App\Helpers\Category as CategoryHelper;
use App\Repositories\Sales\SalesRepositoryInterface as RepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;

class SalesController extends MainController implements InterfaceController
{

    protected $controllerView = 'admin.pages.sales.';
    protected $controllerName = 'sales';
    protected $mainModel;
    protected $categoryModel;

    public function __construct(RepositoryInterface $repository, CategoryRepositoryInterface $categoryModel)
    {
        $this->title = 'Khuyến mãi';
        $this->mainModel = $repository;
        $this->categoryModel = $categoryModel;
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request)
    {
        $this->metaTitle = 'Danh sách khuyến mãi';
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);

        return view($this->controllerView . 'index', [
            'items' => $items,
            'title' => $this->title,
            'metaTitle' => $this->metaTitle
        ]);
    }

    // @Override
    public function form(Request $request)
    {
        $this->metaTitle = "Thêm " . $this->title;
        $this->buttomGroup = "form";
        $this->title = "Thông tin";
        $item = null;
        // $category_condition_list =  $product_condition_list = $product_ids = $category_ids = null;
        if ($request->id !== null) {
            $params["id"] = $request->id;
            $item = $this->mainModel->getItem($params, ['task' => 'frontend-get-item']);
            $this->metaTitle = "Chỉnh sửa " . $this->title;
        }
        return view($this->controllerView . 'form', [
            'title' => $this->title,
            'buttomGroup' => $this->buttomGroup,
            'metaTitle' => $this->metaTitle,
            'item' => $item
        ]);
    }

    // @Override
    public function save(mainRequest $request)
    {
        if ($request->method() == 'POST') {
            $params = $request->all();
            // echo "<pre>";
            // print_r(json_decode($params['condition_sales'],true));
            // die();
            $task = "add-item";
            $notify = "Thêm khuyến mãi thành công!";
            $notify_error = "Thêm khuyến mãi thất bại!";
            if ($params['id'] !== null) {
                $task = "edit-item";
                $notify = "Cập nhật khuyến mãi thành công!";
                $notify_error = "Cập nhật khuyến mãi thất bại!";
            }
            $id = $this->mainModel->saveItem($params, ['task' => $task]);
            if ($request->rollback == 1) {
                if ($id == FALSE) {
                    if (isset($params['id'])) {
                        return redirect()->route($this->controllerName . "/form", ['id' => $params['id']])->with("notify_error", $notify_error);
                    }
                    return redirect()->route($this->controllerName . "/form", ['id' => $id])->with("notify_error", $notify_error);
                }
                return redirect()->route($this->controllerName . "/form", ['id' => $id])->with("notify", $notify);
            } else {
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
    public function listSales(Request $request)
    {
        if (isset($request->id)) {
            $id = $request->id;
            $result = (new \App\Models\ProductSaleItems())->select("product_id", 'quantity_is_uses_product', 'special_percent', 'special_price', 'buy_qty', 'gift_sku', 'gift_qty')->where('product_sales_id', $id)->get();
            $response = array(
                'status' => true,
                'data' => $result->toArray()
            );
        } else {
            $response = array(
                'status' => false
            );
        }
        return json_encode($response);
    }
}
