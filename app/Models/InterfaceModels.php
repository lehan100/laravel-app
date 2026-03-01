<?php

namespace App\Models;

interface InterfaceModels
{
    public function listItems($params = null, $options = null);
    public function getItem($params = null, $options = null);
    public function saveItem($params = null, $options = null);
    public function deleteItem($params = null, $options = null);
}
