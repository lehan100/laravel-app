<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MainController;
use App\Http\Controllers\InterfaceController;
use Illuminate\Http\Request;
use App\Helpers\Template as Template;
use App\Services\AdminFilter;
use App\Helpers\Order\Invoice;
use App\Helpers\Order\Timeline;
//use App\Models\Province as mainModel;
use App\Repositories\Order\OrderEloquentRepository as RepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface as ProductRepositoryInterface;
use App\Repositories\Province\ProvinceRepositoryInterface;
use App\Repositories\Inventory\InventoryRepositoryInterface;

class OrderController extends MainController
{

    protected $controllerView = 'admin.pages.order.';
    protected $controllerName = 'order';
    protected $mainModel;
    protected $proviceModel;
    protected $productModel;
    protected $inventoryModel;
    protected $timeline;
    protected $filterService;
    public function __construct(RepositoryInterface $repository, ProvinceRepositoryInterface $proviceModel, ProductRepositoryInterface $productModel, InventoryRepositoryInterface $inventoryModel, Timeline $timeline, AdminFilter $filterService)
    {
        $this->title = 'Đơn hàng';
        $this->mainModel = $repository;
        $this->proviceModel = $proviceModel;
        $this->productModel = $productModel;
        $this->inventoryModel = $inventoryModel;
        $this->timeline = $timeline;
        $sessionkey = $this->controllerName . "_filter";
        $this->filterService = $filterService;
        $this->filterService->setSessionKey($sessionkey);
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    public function index(Request $request)
    {
        $this->metaTitle = 'Danh sách ' . $this->title;
        $this->params['filter'] = $this->filterService->getData();
        //query
        $items = $this->mainModel->listItems($this->params, ['task' => 'admin-list-items']);

        //Report
        //$reportTotalByStatus = $this->mainModel->listItems($this->params, ['task' => 'list-item-report-purchases-status']);
        //$reportCountByStatus = $this->mainModel->listItems($this->params, ['task' => 'list-item-report-count-status']);

        return view($this->controllerView . 'index', [
            'items' => $items,
            'title' => $this->title,
            'metaTitle' => $this->metaTitle,
            'filter' => $this->params['filter']

        ]);
    }

    public function view(Request $request)
    {
        if (isset($request->id)) {
            $id = $request->id;
            $this->metaTitle = "Chi tiết đơn hàng " . sprintf('#%06d', $id);
            $this->buttomGroup = "form";
            $this->title = "Thông tin";
            $invoice = $this->mainModel->getItem(['id' => $request->id], ['task' => 'get-item']);
            // echo "<pre>";
            // print_r($invoice->toArray());die();
            $view = $this->controllerView . 'view';
            if($invoice){
                if($invoice->order_status== 'processed'){
                    $view = $this->controllerView . 'invoice'; 
                }
                if($invoice->order_status== 'processed' && $invoice->shipping_status== 'processed'){
                    $view = $this->controllerView . 'shipping'; 
                }
            }
            // if ($invoice  && $invoice->shipping_status != 'processed') {
            //     $view = $this->controllerView . 'shipping';
            // } else if ($invoice  && $invoice->shipping_status == 'awaiting') {
                
            // }
            return view($view, [
                'title' => $this->title,
                'metaTitle' => $this->metaTitle,
                'invoice' => $invoice
            ]);
        }
    }

    public function postInvoice(Request $request)
    {
        if (isset($request->id)) {
            $order_id = $request->id;
            $invoice = $this->mainModel->getItem(['id' => $request->id], ['task' => 'get-item']);
            if (!$invoice ||  $invoice->order_status != "awaiting") {
                return redirect()->route($this->controllerName . "/view", ['id' => $order_id])->with('error', "Cập nhật thất bại");
            }
            $invoiceID = $invoice->invoice_id;
            $order_status = "processed";
            // Add TimeLine
            $timeline = $invoice->timeline;
            $dataTimeLine = json_decode($timeline->comments, true);
            $this->timeline->setData($dataTimeLine);
            $order_comment = config("configs.order_status")[$order_status]['comment'];
            $this->timeline->createdTimeLine($order_comment, Auth::user()->username);
            $this->mainModel->saveItem(['id' => $order_id, 'invoice_id' => $invoiceID, 'order_status' => $order_status, 'timeline' => $this->timeline->getData()], ['task' => 'update-invoice']);
            return redirect()->route($this->controllerName . "/view", ['id' => $order_id])->with('success', "Tạo mã vận đơn thành công");
        }
        return redirect()->route($this->controllerName)->with('error', "Cập nhật thất bại");
    }
    public function postShipping(Request $request)
    {
        if (isset($request->id)) {
            $order_id = $request->id;
            $invoice = $this->mainModel->getItem(['id' => $request->id], ['task' => 'get-item']);
            if (!$invoice ||  $invoice->invoice_id == "") {
                if (!isset($request->type) && $request->type != "cancel") {
                    return redirect()->route($this->controllerName . "/view", ['id' => $order_id])->with('notify_error', "Cập nhật thất bại");
                }
            }

            $timeline = $invoice->timeline;
            $dataTimeLine = json_decode($timeline->comments, true);
            $this->timeline->setData($dataTimeLine);
            if (isset($request->type)) {
                switch ($request->type) {
                    case 'success':
                        $shipping_status = "success";
                        $order_status = "success";
                        $payment_status = "success";
                        $task_inventory = 'update-qty-inventory-success';
                        $shipping_comment = config("configs.shipping_status")[$shipping_status]['comment'];
                        $order_comment = config("configs.order_status")[$order_status]['comment'];
                        $payment_comment = config("configs.payment_status")[$payment_status]['comment'];
                        $message = "Đã giao hàng thành công";
                        break;
                    case 'cancel':
                        $shipping_status = "cancel";
                        $order_status = "cancel";
                        $payment_status = "cancel";
                        $task_inventory = 'update-qty-inventory-cancel';
                        if ($invoice->payment_status == 'success') {
                            $payment_status = "cancel_refund";
                        }
                        $shipping_comment = config("configs.shipping_status")[$shipping_status]['comment'];
                        $order_comment = config("configs.order_status")[$order_status]['comment'];
                        $payment_comment = config("configs.payment_status")[$payment_status]['comment'];
                        $message = "Hủy đơn hàng thành công";
                        break;
                }
                $this->timeline->createdTimeLine($shipping_comment, Auth::user()->username);
                $this->timeline->createdTimeLine($payment_comment, Auth::user()->username);
                $this->timeline->createdTimeLine($order_comment, Auth::user()->username);
                $dataUpdate = ['id' => $order_id, 'order_status' => $order_status, 'shipping_status' => $shipping_status, 'payment_status' => $payment_status, 'timeline' => $this->timeline->getData()];
            } else {
                $shipping_status = "processed";
                $order_status = "processed";
                $order_comment = config("configs.shipping_status")[$shipping_status]['comment'];
                $this->timeline->createdTimeLine($order_comment, Auth::user()->username);
                $dataUpdate = ['id' => $order_id, 'shipping_status' => $shipping_status, 'timeline' => $this->timeline->getData()];
                $message = "Xác nhận vận chuyển thành công";
            }
            // Add TimeLine
            if ($this->mainModel->saveItem($dataUpdate, ['task' => 'update-invoice'])) {
                $listProducts = (new \App\Models\OrderItems())->where("order_id", $order_id)->get();

                foreach ($listProducts as $product) {
                    $param = [
                        'qty' => $product['qty'],
                        'id' => $product['product_id'],
                        'order_id' => $order_id
                    ];
                    if ($shipping_status == 'success' && $order_status == 'success') {
                        $this->productModel->saveItem($param, ['task' => 'update-hit-order']);
                    }
                    if (isset($task_inventory)) {
                        $this->inventoryModel->saveItem($param, ['task' => $task_inventory]);
                    }
                    if ($product->gift != 'null') {
                        $gifts = json_decode($product->gift, true);
                        if (isset($gifts['gift_items'])) {
                            foreach ($gifts['gift_items'] as $gift) {
                                //print_r($gift);die();
                                $param_gift = [
                                    'qty' => $gifts['qty'],
                                    'id' => $gift['id'],
                                    'order_id' => $order_id
                                ];

                                if ($shipping_status == 'success' && $order_status == 'success') {
                                    $this->productModel->saveItem($param_gift, ['task' => 'update-hit-order']);
                                }
                                if (isset($task_inventory)) {
                                    $this->inventoryModel->saveItem($param_gift, ['task' => $task_inventory]);
                                }
                            }
                        }
                    }
                }
            }
            return redirect()->route($this->controllerName . "/view", ['id' => $order_id])->with('success', $message);
        }
        return redirect()->route($this->controllerName)->with('error', "Cập nhật thất bại");
    }
    public function postPayment(Request $request)
    {
        if (isset($request->id)) {
            $order_id = $request->id;
            $invoice = $this->mainModel->getItem(['id' => $request->id], ['task' => 'get-item']);
            if (!$invoice ||  $invoice->invoice_id == "") {
                if (!isset($request->type) && $request->type != "cancel") {
                    return redirect()->route($this->controllerName . "/view", ['id' => $order_id])->with('notify_error', "Cập nhật thất bại");
                }
            }

            $timeline = $invoice->timeline;
            $dataTimeLine = json_decode($timeline->comments, true);
            $this->timeline->setData($dataTimeLine);
            $payment_status = "success";
            $payment_comment = config("configs.payment_status")[$payment_status]['comment'];
            $this->timeline->createdTimeLine($payment_comment, Auth::user()->username);
            $dataUpdate = ['id' => $order_id, 'payment_status' => $payment_status, 'timeline' => $this->timeline->getData()];
            if ($this->mainModel->saveItem($dataUpdate, ['task' => 'update-invoice'])) {
                return redirect()->route($this->controllerName . "/view", ['id' => $order_id])->with('success', "Cập nhật trạng thái thanh toán thành công");
            }
        }
        return redirect()->route($this->controllerName)->with('error', "Cập nhật trạng thái thanh toán thất bại");
    }
    public function postTimeline(Request $request)
    {
        if (isset($request->id)) {
            if ($request->comment == "") {
                return response()->json(['status' => false, 'error' => ['comment' => "Vui lòng không được để trống"]]);
            }
            $invoice = $this->mainModel->getItem(['id' => $request->id], ['task' => 'get-item']);
            if (!$invoice) {
                return response()->json(['status' => false, 'message' => "Không tìm thấy thông tin đơn hàng"]);
            }
            $timeline = $invoice->timeline;
            $dataTimeLine = json_decode($timeline->comments, true);
            $this->timeline->setData($dataTimeLine);
            $this->timeline->createdTimeLine($request->comment, Auth::user()->username);
            $dataUpdate = ['id' => $request->id, 'timeline' => $this->timeline->getData()];
            $this->mainModel->saveItem($dataUpdate, ['task' => 'update-comment']);
            $listComment = (new \App\Models\OrderTimelines())->where("order_id", $request->id)->first();
            $commens = json_decode($listComment->comments, true);
            foreach ($commens as $k => $comment) {
                $createdAt = \Illuminate\Support\Carbon::parse($comment['date']);
                $commens[$k]['date'] =  $createdAt->format('d/m/Y h:m:s');
            }
            return response()->json(['status' => true, 'message' => "Thêm comment thành công", 'data' => $commens]);
        }
        return response()->json(['status' => false, 'message' => "Thêm comment thất bại"]);
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
    public function multiple(Request $request)
    {
        $status = FALSE;
        $message_success = config('configs.messages.update_cancel_success');
        $message_error = config('configs.messages.update_cancel_error');
        switch ($request->type) {
            case 'cancel':
                if (isset($request->aid) && count($request->aid) > 0) {
                    $shipping_status = "cancel";
                    $order_status = "cancel";
                    $payment_status = "cancel";
                    
                    foreach ($request->aid as $id) {
                        $dataUpdate = ['id' => $id, 'order_status' => $order_status, 'shipping_status' => $shipping_status, 'payment_status' => $payment_status];
                        $status = $this->mainModel->saveItem($dataUpdate, ['task' => 'admin-update-multi-cancel']);
                    }
                }
                break;
        }

        if ($status) {
            return redirect()->route($this->controllerName)->with('notify', $message_success);
        }
        return redirect()->route($this->controllerName)->with('notify_error', $message_error);
    }
}
