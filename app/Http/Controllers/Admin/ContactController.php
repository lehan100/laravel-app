<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
//use App\Http\Requests\WardPostRequest as mainRequest;
use App\Helpers\Template as Template;
//use App\Models\District as mainModel;
//use App\Models\Province as districtModel;
use App\Repositories\Contact\ContactRepositoryInterface as RepositoryInterface;
use App\Services\AdminFilterService;

class ContactController extends MainController implements InterfaceController {

    protected $controllerView = 'admin.pages.contact.';
    protected $controllerName = 'contact';
    protected $mainModel;
    protected $filterService;

    public function __construct(RepositoryInterface $repository, AdminFilterService $filterService) {
        $this->mainModel = $repository;
        $sessionkey = $this->controllerName . "_filter";
        $this->filterService = $filterService;
        $this->filterService->setSessionKey($sessionkey);
        $this->title = 'Liên hệ, Góp ý';
        //$this->mainModel = new mainModel();
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'filter' => $this->filterService, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index(Request $request) {
        $this->metaTitle = 'Danh sách';
       
        $this->params['filter'] = $this->filterService->getFilter();
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);
        
        return view($this->controllerView . 'index', [
            'items' => $items,
            'title' => $this->title,
            'metaTitle' => $this->metaTitle
        ]);
    }

    // @Override
    public function form(Request $request) {
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
    public function save(Request $request) {
        if ($request->method() == 'POST') {
            $params = $request->all();

            $task = "add-item";
            $notify = "Thêm phần tử thành công!";

            if ($params['id'] !== null) {
                $task = "edit-item";
                $notify = "Cập nhật phần tử thành công!";
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
    public function delete(Request $request) {
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
    public function status(Request $request) {
        if (isset($request->id)) {
            $params["id"] = $request->id;
            $params["status"] = $request->status;
            $statusResult = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $xhtml = Template::showStatusContact($this->controllerName, $statusResult, $params["id"]);
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
    public function multiple(Request $request) {
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

    public function filter(Request $request) {

        $key = $request->key;
        $value = $request->val;
//        $data = array($key => $value);
//        if ($request->session()->has($this->filter)) {
//            $data = $request->session()->get($this->filter);
//            if ($value == 0) {
//                unset($data[$key]);
//            } else {
//                $data[$key] = $value;
//            }
//        }
//        $request->session()->put($this->filter, $data);
        $this->filterService->setFilter(['key' => $key, 'value' => $value]);
        return json_encode($this->filterService->getFilter());
    }
}
