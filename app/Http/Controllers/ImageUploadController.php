<?php

namespace App\Http\Controllers;

//use Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Helpers\Filter;
use App\Helpers\FileUpload;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{

    protected $ImageManager;
    public $configPath;

    public function __construct()
    {
        $this->ImageManager = new ImageManager(new Driver());
        $this->configPath = config('image.path');
    }

    public function storeImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $configPath = config('image.path.wysiwyg');
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            //            $fileNameWebp = Filter::setUrlKey($fileName) . '_' . time() . '.webp';
            $fileName = Filter::setUrlKey($fileName) . '_' . time() . '.' . $extension;
            $filePath = public_path($configPath['path']);
            $request->file('upload')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->resizeDown(1200, null)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = "/" . $configPath['path'] . '/' . $fileName;
            return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
    }

    public function storeProduct(Request $request)
    {
        
        if ($request->hasFile('picture')) {
            $configPath = $this->configPath['product'];
            $originName = $request->file('picture')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = strtolower($request->file('picture')->getClientOriginalExtension());
            //            $fileNameWebp = Filter::setUrlKey($fileName) . '_' . time() . '.webp';
            $fileName = Filter::setUrlKey($fileName) . '-' . time() . '.' . $extension;
            $filePath = public_path($configPath['temp']);
            $request->file('picture')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->pad(800, 800)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = asset($configPath['temp'] . '/' . $fileName);
            return response()->json(['picture' => $fileName, 'uploaded' => 1, 'status' => true, 'url' => $url]);
        }

        return response()->json(['status' => FALSE]);
    }
    public function storeProductRating(Request $request)
    {

        if ($request->hasFile('picture')) {
            $configPath = $this->configPath['rating'];
            $originName = $request->file('picture')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = strtolower($request->file('picture')->getClientOriginalExtension());
            $fileName = Filter::setUrlKey($fileName) . '-' . time() . '.' . $extension;
            $filePath = public_path($configPath['temp']);
            $request->file('picture')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->scale(width: 1000)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = asset($configPath['temp'] . '/' . $fileName);
            return response()->json(['picture' => $fileName, 'uploaded' => 1, 'status' => true, 'url' => $url]);
        }
        return response()->json(['status' => FALSE]);
    }
    public function storeProductDelete(Request $request)
    {
        if ($request->has('picture')) {
            $configPath = $this->configPath['product'];
            $fileName = $request->picture;
            $status = FileUpload::deleteFiles($configPath['temp'], $fileName);
            return response()->json(['status' => $status]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storeProductOption(Request $request)
    {

        if ($request->hasFile('option_picture')) {
            $configPath = $this->configPath['product_option'];
            $originName = $request->file('option_picture')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = strtolower($request->file('option_picture')->getClientOriginalExtension());
            $fileName = Filter::setUrlKey($fileName) . '-' . time() . '.' . $extension;
            $filePath = public_path($configPath['temp']);
            $request->file('option_picture')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->pad(800, 800)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = asset($configPath['temp'] . '/' . $fileName);
            return response()->json(['picture' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storeProductOptionDelete(Request $request)
    {
        if ($request->has('picture')) {
            $configPath = $this->configPath['product_option'];
            $fileName = $request->picture;
            $status = FileUpload::deleteFiles($configPath['temp'], $fileName);
            return response()->json(['status' => $status]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storeCategory(Request $request)
    {

        if ($request->hasFile('picture')) {
            $configPath = $this->configPath['category'];
            $originName = $request->file('picture')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = strtolower($request->file('picture')->getClientOriginalExtension());
            $fileName = Filter::setUrlKey($fileName) . '-' . time() . '.' . $extension;
            $filePath = public_path($configPath['temp']);
            $request->file('picture')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->resizeDown(1200, null)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = asset($configPath['temp'] . '/' . $fileName);
            return response()->json(['picture' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storeCategoryDelete(Request $request)
    {
        if ($request->has('picture')) {
            $configPath = $this->configPath['category'];
            $fileName = $request->picture;
            $status = FileUpload::deleteFiles($configPath['temp'], $fileName);
            return response()->json(['status' => $status]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storePost(Request $request)
    {

        if ($request->hasFile('picture')) {
            $configPath = $this->configPath['post'];
            $originName = $request->file('picture')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = strtolower($request->file('picture')->getClientOriginalExtension());
            $fileName = Filter::setUrlKey($fileName) . '-' . time() . '.' . $extension;
            $filePath = public_path($configPath['temp']);
            $request->file('picture')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->resizeDown(1200, null)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = asset($configPath['temp'] . '/' . $fileName);
            return response()->json(['picture' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storePostDelete(Request $request)
    {
        if ($request->has('picture')) {
            $configPath = $this->configPath['post'];
            $fileName = $request->picture;
            $status = FileUpload::deleteFiles($configPath['temp'], $fileName);
            return response()->json(['status' => $status]);
        }
        return response()->json(['status' => FALSE]);
    }
    public function storeAttributeOption(Request $request)
    {

        if ($request->hasFile('option_picture')) {
            $configPath = $this->configPath['attribute_set'];
            $originName = $request->file('option_picture')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = strtolower($request->file('option_picture')->getClientOriginalExtension());
            $fileName = Filter::setUrlKey($fileName) . '-' . time() . '.' . $extension;
            $filePath = public_path($configPath['temp']);
            $request->file('option_picture')->move($filePath, $fileName);
            $img = $this->ImageManager->read($filePath . "/" . $fileName);
            $img->pad(320, 180)->toWebp(60)->save($filePath . "/" . $fileName);
            $url = asset($configPath['temp'] . '/' . $fileName);
            return response()->json(['picture' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
        return response()->json(['status' => FALSE]);
    }

    public function storeAttributeOptionDelete(Request $request)
    {
        if ($request->has('picture')) {
            $configPath = $this->configPath['attribute_set'];
            $fileName = $request->picture;
            $status = FileUpload::deleteFiles($configPath['temp'], $fileName);
            return response()->json(['status' => $status]);
        }
        return response()->json(['status' => FALSE]);
    }
}
