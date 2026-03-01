<?php

namespace App\Helpers\Order;

use \Illuminate\Support\Carbon;
use Illuminate\Support\Arr;

class Timeline
{
    protected $TIMELINE;
    protected $ORDER_ID;

    public function __construct()
    {
        $this->TIMELINE = [];
    }
    public function setData($data)
    {
        $this->TIMELINE = $data;
        return $this;
    }

    public function getData()
    {
        return $this->TIMELINE;
        return $this;
    }
    public function createdOrder()
    {
        $this->createdTimeLine("Đặt hàng thành công", 'customer');
        return $this;
    }

    public function createdTimeLine($comment = "", $modify = "", $description = "")
    {

        if ($comment != "" && $modify != "") {
            $dt = Carbon::now('Asia/Ho_Chi_Minh')->toDateTimeString();
            $itemTimeline = [
                'date' => $dt,
                'modify' => $modify,
                'comment' => $comment,
                'description' => $description,
            ];
            $this->TIMELINE = Arr::prepend($this->TIMELINE, $itemTimeline);
        }
    }
}
