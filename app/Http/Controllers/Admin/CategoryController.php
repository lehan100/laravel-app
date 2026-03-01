<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Arr;
use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryPostRequest as mainRequest;
use App\Helpers\Template as Template;
use App\Helpers\Category as CategoryHelper;
//use App\Models\Province as mainModel;
use App\Repositories\Category\CategoryRepositoryInterface as RepositoryInterface;

class CategoryController extends MainController implements InterfaceController
{

    protected $controllerView = 'admin.pages.category.';
    protected $controllerName = 'category';
    protected $mainModel;

    public function __construct(RepositoryInterface $repository)
    {
        $this->title = 'danh mục';
        $this->mainModel = $repository;
        $this->params['pagination']['totalItemsPerPage'] = 100;
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request)
    {
        $this->metaTitle = 'Danh sách ' . $this->title;
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);
        $navigation = (new CategoryHelper())->generateNavigation($items);
        $result = (new CategoryHelper())->Hierarchy($items, true);
        $id = $request->id;
        return view($this->controllerView . 'index', [
            'items' => $items,
            'navigation' => $navigation,
            'result' => $result,
            'id' => $id,
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
        if ($request->id !== null) {
            $params["id"] = $request->id;

            $item = $this->mainModel->getItem($params, ['task' => 'get-item']);
            $this->metaTitle = "Chỉnh sửa " . $this->title;
        }
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items-selector']);
        $categorySellector = (new CategoryHelper())->Hierarchy($items, false);
        return view($this->controllerView . 'form', [
            'title' => $this->title,
            'buttomGroup' => $this->buttomGroup,
            'metaTitle' => $this->metaTitle,
            'item' => $item,
            'categorySellector' => $categorySellector
        ]);
    }

    public function formajax(Request $request)
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
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items-selector']);
        $categorySellector = (new CategoryHelper())->Hierarchy($items, false);
        $view = view($this->controllerView . 'formajax', [
            'buttomGroup' => $this->buttomGroup,
            'item' => $item,
            'parent_id' => $request->parent_id,
            'categorySellector' => $categorySellector
        ])->render();
        return response()->json(['item' => $item, 'html' => $view, 'url' => route($this->controllerName, ['id' => $request->id,])]);
    }
    public function categorySelect(Request $request)
    {
        try {
            $this->params['page'] = [2];
            $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items-selector']);
            $checked = $request->selected;
            if ($checked != "") {
                $checked = explode(",", $checked);
            }
            $navigation = (new CategoryHelper())->generateNavigationSelect($items, $checked);
            $view = view($this->controllerView . 'ajax/select', [
                'navigation' => $navigation,
            ])->render();
            return response()->json(['html' => $view, 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Get Data Error'], 200);
        }
    }
    public function getItemsCategory(Request $request)
    {
        if ($request->ids) {
            if ($request->ids != "") {
                $this->params["ids"] = explode(",", $request->ids);
                $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items-by-ids']);
                $itemsCategory = $this->mainModel->listItems(null, ['task' => 'frontend-list-items-breadcrumbs']);
                foreach($items as $key => $item){
                    $itemsBreadcrumbs = (new CategoryHelper())->generateDataBreadcrumb($itemsCategory, $item->id);
                    $array  = Arr::map($itemsBreadcrumbs,function($item){
                        return $item->name;
                    });
                    $items[$key]['name'] = Arr::join($array," / ");
                }
                return response()->json(['status' => true, 'data' => $items], 200);
            }
        }
        return response()->json(['status' => false, 'message' => 'Not Get Items Category'], 200);
    }
    // @Override
    public function save(mainRequest $request)
    {
        if ($request->method() == 'POST') {
            $params = $request->all();
            $task = "add-item";
            $notify = "Thêm phần tử thành công!";

            if ($params['id'] !== null) {
                $task = "edit-item";
                $notify = "Cập nhật phần tử thành công!";
            }
            $id = $this->mainModel->saveItem($params, ['task' => $task]);

            //            if ($request->rollback == 1) {
            //                return redirect()->route($this->controllerName . "/form", ['id' => $id])->with("notify", $notify);
            //            } else {
            return redirect()->route($this->controllerName, ['id' => $id])->with("notify", $notify);
            //            }
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

    public function sort(Request $request)
    {
        $params = $request->all();
        $this->mainModel->saveItem($params, ['task' => 'sort-item']);
        return response()->json(['url' => route($this->controllerName, ['id' => $request->id]), 'id' => $request->id]);
    }
    //    public function sort(Request $request) {
    //        $params = $request->all();
    //        $status = $this->mainModel->saveItem($params, ['task' => 'sort-items']);
    //        $message_success = config('configs.messages.update_status_success');
    //        $message_error = config('configs.messages.update_status_error');
    //        if ($status) {
    //            return redirect()->route($this->controllerName)->with('notify', $message_success);
    //        }
    //        return redirect()->route($this->controllerName)->with('notify_error', $message_error);
    //    }

}
