<?php
namespace App\Repositories\Order;
use App\Repositories\EloquentRepositoryInterface;
interface OrderRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateInventory($shoppingCart,$order_id);
}