<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage as StorageDisk;
use App\Repositories\Province\ProvinceRepositoryInterface;
use App\Services\GHTK;

class Shipping
{
    private $CONFIGS;
    private $dataType;
    protected $GHTK;
    protected $proviceModel;
    public function __construct(ProvinceRepositoryInterface $proviceModel, GHTK $ghtk)
    {
        $this->proviceModel = $proviceModel;
        $this->dataType = config("configs.location");
        $this->GHTK = $ghtk;
        $this->GHTK->setToken('024c788ee3ec0e0620157b56cb4976bef7bc609a');

        if (StorageDisk::disk('main')->exists("settings.json")) {
            $data = StorageDisk::disk('main')->get('settings.json');
            $this->CONFIGS = json_decode($data, true);
        }
    }
    public function getShippingPrice($param, $subtotal = 0, $weight = 0)
    {
        if ($weight > 0 && $subtotal >= 0) {
            if ($subtotal >= $this->CONFIGS['freeshipping_price']) {
                return 0;
            }
            try {
                $province = $this->proviceModel->getItem($param, ['task' => 'frontent-get-item']);
                if ($province) {
                    $city = $province->name;
                    $district =$province->districtOne->name;
                    $ward = $province->districtOne->ward->name;

                    $response = $this->GHTK
                        ->setShippingProvince($city)
                        ->setShippingDistrict($district)
                        ->setShippingWard($ward)
                        ->setShippingAddress($param['address'])
                        ->setValue($subtotal)
                        ->setWeight($weight)
                        ->getShipping();
                    if ($response['success'] == 1) {
                        return $response['fee']['fee'];
                    }
                }
            } catch (\Throwable $th) {
                return null;
            }
        }
        return null;
    }
}
