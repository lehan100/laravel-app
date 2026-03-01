<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Arr;
use App\Http\Resources\ProductCollection as ProductResource;
use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
use App\Http\Requests\CouponPostRequest as mainRequest;
use App\Helpers\Template as Template;
//use App\Models\Province as mainModel;
use App\Helpers\Category as CategoryHelper;
use App\Repositories\Coupon\CouponRepositoryInterface as RepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;

class CouponController extends MainController implements InterfaceController
{

    protected $controllerView = 'admin.pages.coupon.';
    protected $controllerName = 'coupon';
    protected $mainModel;
    protected $categoryModel;

    public function __construct(RepositoryInterface $repository, CategoryRepositoryInterface $categoryModel)
    {
        $this->title = 'Mã giảm giá';
        $this->mainModel = $repository;
        $this->categoryModel = $categoryModel;
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request)
    {
        $this->metaTitle = 'Danh sách mã giảm giá';
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
        $category_condition_list =  $product_condition_list = $product_ids = $category_ids = null;
        if ($request->id !== null) {
            $params["id"] = $request->id;
            $item = $this->mainModel->getItem($params, ['task' => 'get-item']);
            $product_coupon_codes = $item->toArray()['product_coupon_codes'];
            $product_ids =  Arr::pluck(Arr::where($product_coupon_codes, function ($val) {
                return $val['product_id'] > 0;
            }), 'product_id');
            $product_condition_list =  Arr::pluck(Arr::where($product_coupon_codes, function ($val) {
                return $val['product_id'] > 0;
            }), 'product_of_coupons.product');
            $category_ids =  Arr::pluck(Arr::where($product_coupon_codes, function ($val) {
                return $val['category_id'] > 0;
                //return $val['category_id'] > 0 && count($val['category_of_coupons']) > 0;
            }), 'category_id');
            
            if (count($category_ids) > 0) {
                $itemsCategory = $this->categoryModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
                foreach ($category_ids as $key => $category_id) {
                    $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $category_id);
                    $array  = Arr::map($itemsBreadcrumbs, function ($item) {
                        return $item->name;
                    });
                    $category_condition_list[$key] = array(
                        'id' => $category_id,
                        'name' => Arr::join($array, " / ")
                    );
                }
            }
            // echo "<pre>";
            // print_r($product_condition_list);
            // die();
            $this->metaTitle = "Chỉnh sửa " . $this->title;
        }
        return view($this->controllerView . 'form', [
            'title' => $this->title,
            'buttomGroup' => $this->buttomGroup,
            'metaTitle' => $this->metaTitle,
            'item' => $item,
            'product_ids' => $product_ids,
            'product_condition_list' => $product_condition_list,
            'category_ids' => $category_ids,
            'category_condition_list' => $category_condition_list
        ]);
    }

    // @Override
    public function save(mainRequest $request)
    {
        if ($request->method() == 'POST') {
            $params = $request->all();

            $task = "add-item";
            $notify = "Thêm mã giảm giá thành công!";

            if ($params['id'] !== null) {
                $task = "edit-item";
                $notify = "Cập nhật mã giảm giá thành công!";
            }
            $id = $this->mainModel->saveItem($params, ['task' => $task]);
            if ($request->rollback == 1) {
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
}
