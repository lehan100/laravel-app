<?php

namespace App\Http\Controllers\Default;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\FrontendController;
use App\Repositories\Province\ProvinceRepositoryInterface;
use App\Repositories\District\DistrictRepositoryInterface;
use App\Repositories\Ward\WardRepositoryInterface;
use App\Services\ShoppingCartInfo;

class ProvinceController extends FrontendController {

    protected $controllerView = 'default.pages.province.';
    protected $controllerViewLayout = 'default.layouts.';
    protected $controllerName = 'province';
    protected $provinceModel;
    protected $districtModel;
    protected $wardModel;

    public function __construct(
            ProvinceRepositoryInterface $provinceModel,
            DistrictRepositoryInterface $districtModel,
            WardRepositoryInterface $wardModel,
    ) {
        parent::__construct();
        $this->provinceModel = $provinceModel;
        $this->districtModel = $districtModel;
        $this->wardModel = $wardModel;
    }

    public function getCity(Request $request) {
        $type = config("configs.location.province");
        $listCity = $this->provinceModel->listItems(options: ['task' => 'frontend-list-items']);
        if (count($listCity) > 0) {
            foreach ($listCity as $key => $item) {
                $listCity[$key]['type'] = $type[$item['type']];
            }
            return response()->json(['status' => true, 'data' => $listCity]);
        }
        return response()->json(['status' => false]);
    }

    public function getDistrict(Request $request) {
        if (isset($request->id)) {
            $listDistrict = $this->districtModel->listItems(params: ['id' => $request->id], options: ['task' => 'frontend-list-items']);
            $type = config("configs.location.district");
            if (count($listDistrict) > 0) {
                foreach ($listDistrict as $key => $item) {
                    $listDistrict[$key]['type'] = $type[$item['type']];
                }
                return response()->json(['status' => true, 'data' => $listDistrict]);
            }
        }
        return response()->json(['status' => false]);
    }

    public function getWard(Request $request) {
        if (isset($request->id)) {
            $listWard = $this->wardModel->listItems(params: ['id' => $request->id], options: ['task' => 'frontend-list-items']);
            $type = config("configs.location.ward");
            if (count($listWard) > 0) {
                foreach ($listWard as $key => $item) {
                    $listWard[$key]['type'] = $type[$item['type']];
                }
                return response()->json(['status' => true, 'data' => $listWard]);
            }
        }
        return response()->json(['status' => false]);
    }
}
