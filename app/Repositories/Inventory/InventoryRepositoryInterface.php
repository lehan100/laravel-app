<?php
namespace App\Repositories\Inventory;
use App\Repositories\EloquentRepositoryInterface;
interface InventoryRepositoryInterface extends EloquentRepositoryInterface
{
    public function updateTimeLine($params);
}