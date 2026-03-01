<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\MainController;
use Illuminate\Http\Request;
use App\Http\Requests\SettingsPostRequest as mainRequest;
use App\Models\Storage as mainModel;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Post\PostRepositoryInterface;
use App\Repositories\Sales\SalesRepositoryInterface;
use Illuminate\Support\Facades\Artisan;
class SettingsController extends MainController
{

    protected $controllerView = 'admin.pages.settings.';
    protected $controllerName = 'settings';
    protected $model;
    protected $categoryModel;
    protected $productModel;
    protected $postModel;
    protected $saleModel;
    public $skipStorage = [
        '_token',
        'label_key',
        'label_value'
    ];

    public function __construct(
        mainModel $model,
        CategoryRepositoryInterface $categoryModel,
        ProductRepositoryInterface $productModel,
        PostRepositoryInterface $postModel,
        SalesRepositoryInterface $saleModel
    ) {
        $this->model = $model;
        $this->categoryModel = $categoryModel;
        $this->productModel = $productModel;
        $this->postModel = $postModel;
        $this->saleModel = $saleModel;
        $this->title = 'cấu hình hệ thống';
        view()->share(['mainTitle' => $this->title, 'params' => $this->params, 'isDataTable' => $this->isDataTable, 'buttomGroup' => $this->buttomGroup, 'controllerName' => $this->controllerName]);
    }

    // @Override
    public function index()
    {
        $this->metaTitle = 'Cấu hình hệ thống';
        //query
        $items = [];
        if (Storage::disk('main')->exists("settings.json")) {
            $data = Storage::disk('main')->get('settings.json');
            $items = json_decode($data, true);
        } else {
            $dataConfig = $this->model->getStorage("settings");
            if ($dataConfig) {
                $items = json_decode($dataConfig->data, true);
            }
        }
        $locals = [];
        if (Storage::disk('main')->exists("locals.json")) {
            $data = Storage::disk('main')->get('locals.json');
            $locals = json_decode($data, true);
        } else {
            $dataLocals = $this->model->getStorage("locals");
            if ($dataLocals) {
                $items = json_decode($dataLocals->data, true);
            }
        }

        return view($this->controllerView . 'index', [
            'title' => $this->title,
            'item' => $items,
            'locals' => $locals,
            'metaTitle' => $this->metaTitle
        ]);
    }

    public function save(mainRequest $request)
    {
        $params = $request->all();
        if (isset($params['label_key']) && count($params['label_key']) > 0) {
            $locals = [];
            foreach ($params['label_key'] as $key => $val) {
                $locals[$val] = $params['label_value'][$key];
            }
            Storage::disk('main')->put('locals.json', json_encode($locals));
            $this->model->updateOrCreate(['entype_id' => 'locals'], ['data' => json_encode($locals)]);
        }
        foreach ($this->skipStorage as $item) {
            unset($params[$item]);
        }
        Storage::disk('main')->put('settings.json', json_encode($params));
        $this->model->updateOrCreate(['entype_id' => 'settings'], ['data' => json_encode($params)]);

        $notify = "Lưu thông tin thành công!";
        return redirect()->route($this->controllerName)->with("notify", $notify);
    }

    public function sitemap(Request $request)
    {
        $this->metaTitle = 'Tạo SiteMap';
        $configPath = config('image.path');
        $categories = $this->categoryModel->listItems(options: ['task' => 'admin-list-items-sitemap']);
        $posts = $this->postModel->listItems(options: ['task' => 'admin-list-items-sitemap']);
        $products = $this->productModel->listItems(options: ['task' => 'admin-list-items-sitemap']);
        $sales = $this->saleModel->listItems(options: ['task' => 'admin-list-items-sitemap']);
        $xsitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $xsitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
        $xsitemap .= '<url>
				<loc>' . url('/') . '</loc>
				<changefreq>daily</changefreq>
				<priority>1</priority>
			</url>
			';
        if (count($categories) > 0) {
            foreach ($categories as $item) {
                $url = $item->url->path;
                $picture = $item->picture;
                $pictureUrl = ($picture != "") ? '<image:image>
                <image:loc>' . asset($configPath['category']['path'] . '/' . $picture) . '</image:loc>
                </image:image>' : "";
                $xsitemap .= '<url>
				<loc>' . url($url) . '</loc>
				<changefreq>daily</changefreq>
				<priority>0.9</priority>
                ' . $pictureUrl . '
			</url>
			';
            }
        }
        if (count($products) > 0) {
            foreach ($products as $item) {
                $url = $item->url->path;
                $picture = $item->picture;
                $pictureUrl = ($picture != "") ? '<image:image>
                <image:loc>' . asset((new \App\Helpers\Product\Image())->getLinkDefault($item, 'large')) . '</image:loc>
                </image:image>' : "";
                $xsitemap .= '<url>
				<loc>' . url($url) . '</loc>
				<changefreq>daily</changefreq>
				<priority>0.8</priority>
                ' . $pictureUrl . '
			</url>
			';
            }
        }
        if (count($posts) > 0) {
            foreach ($posts as $item) {
                $url = $item->url->path;
                $picture = $item->picture;
                $pictureUrl = ($picture != "") ? '<image:image>
                <image:loc>' . asset($configPath['post']['path'] . '/' . $picture) . '</image:loc>
                </image:image>' : "";
                $xsitemap .= '<url>
				<loc>' . url($url) . '</loc>
				<changefreq>daily</changefreq>
				<priority>0.8</priority>
                ' . $pictureUrl . '
			</url>
			';
            }
        }
        if (count($sales) > 0) {
            foreach ($sales as $item) {
                $url = $item->url->path;
                $xsitemap .= '<url>
				<loc>' . url($url) . '</loc>
				<changefreq>daily</changefreq>
				<priority>0.8</priority>
			</url>
			';
            }
        }
        $xsitemap .= '</urlset>';
        Storage::disk('main_public')->put('sitemap.xml', $xsitemap);
        return view($this->controllerView . 'sitemap', [
            'title' => $this->metaTitle,
            'metaTitle' => $this->metaTitle
        ]);
    }
}
