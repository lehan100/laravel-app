<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Repositories\Order\OrderRepositoryInterface as OrderRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface as ProductRepositoryInterface;

class DashboardController extends MainController
{
   protected $controllerView = 'admin.pages.dashboard.';
   protected $controllerName = 'admin';
   protected $orderModel;
   protected $productModel;
   public function __construct(OrderRepositoryInterface $orderModel, ProductRepositoryInterface $productModel)
   {
      $this->metaTitle = 'Admin Control Panel';
      $this->orderModel = $orderModel;
      $this->productModel = $productModel;
   }

   // @Override
   public function index()
   {
      //Search Term
      $listSearchTerm =  (new \App\Models\SearchTerms())->take(20)->get();
      // Report Product
      $productViewer = $this->productModel->listItems(['hit' => 'hit_viewer'], ['task' => 'admin-list-items-report']);
      $productSeller = $this->productModel->listItems(['hit' => 'hit_order'], ['task' => 'admin-list-items-report']);
      //Report Order
      $listNewOrder = $this->orderModel->listItems($this->params, ['task' => 'list-item-new-order']);
      //echo "<pre>";  print_r($listSearchTerm->toArray());die();
      $reportTotalByStatus = $this->orderModel->listItems($this->params, ['task' => 'list-item-report-purchases-status']);
      $reportCountByStatus = $this->orderModel->listItems($this->params, ['task' => 'list-item-report-count-status']);
      return view($this->controllerView . 'index', [
         'metaTitle' => $this->metaTitle,
         'productViewer' => $productViewer,
         'productSeller' => $productSeller,
         'listNewOrder' => $listNewOrder,
         'reportTotalByStatus' => $reportTotalByStatus,
         'reportCountByStatus' => $reportCountByStatus,
         'listSearchTerm' => $listSearchTerm
      ]);
   }
}
