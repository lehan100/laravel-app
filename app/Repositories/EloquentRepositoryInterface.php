<?php
namespace App\Repositories;

interface EloquentRepositoryInterface
{
    public function find(int $id);
    public function listItems($params = null, $options = null);
    public function getItem($params = null, $options = null);
    public function saveItem($params = null, $options = null);
    public function deleteItem($params = null, $options = null);
}