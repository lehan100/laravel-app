<?php

namespace App\Helpers\Product;

class Attribute {

    protected $_data;

    public function __construct($data) {
        $this->_data = $data;
    }

    public function getParams($params) {
        $arrParam = [];
        foreach ($params as $alias => $param) {
            $find = $this->_data->filter(function ($d) use ($alias) {
                return $d['alias'] == $alias;
            });
            if (count($find) > 0) {
                $data = explode(",", $param);
                $arrParam[$alias] = $data;
                // foreach ($data as $id) {
                //     $arrParam[$alias] = $id;
                // }
            }
        }
        return $arrParam;
    }
}
