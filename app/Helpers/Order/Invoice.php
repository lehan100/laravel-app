<?php

namespace App\Helpers\Order;

use Illuminate\Support\Str;

class Invoice
{
    private $INVOICE_ID;
    public function generate($order_id = 0)
    {
        $this->INVOICE_ID = Str::upper(Str::random(10)) . $order_id;
        return $this->INVOICE_ID;
    }
}
