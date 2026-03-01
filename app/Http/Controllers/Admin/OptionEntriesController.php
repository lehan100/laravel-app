<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
use App\Http\Requests\OptionEntriesPostRequest as mainRequest;
use App\Helpers\Template as Template;
//use App\Models\Province as mainModel;
use App\Repositories\Store\ProductOptionEntriesEloquentRepository as RepositoryInterface;

class OptionEntriesController extends MainController implements InterfaceController
{

    protected $controllerView = 'admin.pages.optionentries.';
    protected $controllerName = 'optionEntries';
    protected $mainModel;

    public function __construct(RepositoryInterface $repository)
    {
        $this->title = 'option';
        $this->mainModel = $repository;
        view()->share(['mainTitle' => ucfirst($this->title), 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request)
    {
        $this->metaTitle = 'List Product ' . ucfirst($this->title).' Entries';
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);
        return view($this->controllerView . 'index', [
            'items' => $items,
            'title' => 'Product Option Entries',
            'metaTitle' => $this->metaTitle,
        ]);
    }

    // @Override
    public function form(Request $request)
    {
        $this->metaTitle = "Add " . ucfirst($this->title);
        $this->buttomGroup = "form";
        $this->title = "Infomation";
        $item = null;
        if ($request->id !== null) {
            $params["id"] = $request->id;

            $item = $this->mainModel->getItem($params, ['task' => 'get-item']);
            $this->metaTitle = "Edit " . $this->title;
        }
        return view($this->controllerView . 'form', [
            'title' => $this->title,
            'buttomGroup' => $this->buttomGroup,
            'metaTitle' => $this->metaTitle,
            'item' => $item,
        ]);
    }

    // @Override
    public function save(mainRequest $request)
    {
        if ($request->method() == 'POST') {
            $params = $request->all();
            $task = "add-item";
            $notify = "Thêm phần tử thành công!";
            $notify_error = "Thêm phần tử  thất bại!";

            if ($params['id'] !== null) {
                $task = "edit-item";
                $notify = "Cập nhật phần tử thành công!";
                $notify_error = "Cập nhật phần tử  thất bại!";
            }
           
            $params['task-main'] = $task;
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

    public function attribute(Request $request)
    {
        $type = $request->type;
        if ($request->item != "null") {
            $item = json_decode($request->item, true);
            $view = view($this->controllerView . 'plugins/option_attribute', [
                'items' => $item['attributes'],
                'type' => $type
            ])->render();
        } else {
            $view = view($this->controllerView . 'plugins/option_attribute', [
                'type' => $type
            ])->render();
        }
        return response()->json(['html' => $view]);
    }

    public function value(Request $request)
    {
        $type = $request->type;
        $view = view($this->controllerView . 'plugins/option_value', [
            'type' => $type,
        ])->render();
        return response()->json(['html' => $view]);
    }
}
