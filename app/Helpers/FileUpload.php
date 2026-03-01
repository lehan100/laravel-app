<?php

namespace App\Helpers;
use Intervention\Image\Drivers\Gd\Driver;
//use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;
use Exception;

class FileUpload
{

    public static function deleteFiles($public_path, $fileName)
    {
        $filePath = public_path($public_path) . "/" . $fileName;
        if (File::exists($filePath)) {
            File::delete($filePath);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function resizeImages($fileName)
    {
        try {
            $sizes = config('image.sizes.product');
            $configPath = config('image.path.product');
            $fileTembPath = public_path($configPath['temp']);
            foreach ($sizes as $key => $val) {
                $filePath = public_path($configPath['path'] . '/' . $key);
                if (!is_dir($filePath)) {
                    mkdir($filePath, 777, true);
                }
                if (file_exists($fileTembPath . "/" . $fileName)) {
                    list($width, $height) = $val;
                    (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                        ->pad($width, $height)->toWebp(60)->save($filePath . "/" . $fileName);
                }
            }
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    public static function resizeImageOption($fileName)
    {
        try {
            $configPath = config('image.path.product_option');
            $fileTembPath = public_path($configPath['temp']);
            $filePath = public_path($configPath['path']);
            $size = config('image.sizes.option');
            if (!is_dir($filePath)) {
                mkdir($filePath, 777, true);
            }
            list($width, $height) = $size;
            if (file_exists($fileTembPath . "/" . $fileName)) {
                (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                    ->pad($width, $height)->toWebp(60)->save($filePath . "/" . $fileName);
            }
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    public static function resizeImageAttribute($fileName)
    {
        try {
            $configPath = config('image.path.attribute_set');
            $fileTembPath = public_path($configPath['temp']);
            $filePath = public_path($configPath['path']);
            $size = config('image.sizes.attribute_set');
            if (!is_dir($filePath)) {
                mkdir($filePath, 777, true);
            }
            list($width, $height) = $size;
            if (file_exists($fileTembPath . "/" . $fileName)) {
                (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                    ->pad($width, $height)->toWebp(60)->save($filePath . "/" . $fileName);
            }
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    public static function moveTrash($fileName)
    {
        try {
            // Move Main Picture Product
            $sizes = config('image.sizes.product');
            $configPath = config('image.path.product');
            //            $fileTembPath = public_path($configPath['temp']);
            foreach ($sizes as $key => $val) {
                $fileTembPath = public_path($configPath['path'] . '/' . $key . "/" . $fileName);
                $filePath = public_path($configPath['trash'] . '/' . $key . "/" . $fileName);
                //                if (!is_dir($filePath)) {
                //                    mkdir($filePath, 777, true);
                //                }
                if (file_exists($fileTembPath)) {
                    File::move($fileTembPath, $filePath);
                }
            }
            // Move Option Picture
            $configPath = config('image.path.product_option');
            $fileTembPath = public_path($configPath['path'] . '/' . $fileName);
            $filePath = public_path($configPath['trash'] . '/' . $fileName);
            if (file_exists($fileTembPath)) {
                File::move($fileTembPath, $filePath);
            }
            // Move Attribute Set Picture
            $configPath = config('image.path.attribute_set');
            $fileTembPath = public_path($configPath['path'] . '/' . $fileName);
            $filePath = public_path($configPath['trash'] . '/' . $fileName);
            if (file_exists($fileTembPath)) {
                File::move($fileTembPath, $filePath);
            }
            return true;
        } catch (Exception $exc) {
            return false;
        }
    }

    public static function deletePicture($fileName)
    {
        $sizes = config('image.sizes.product');
        $configPath = config('image.path.product');
        foreach ($sizes as $key => $val) {
            $filePath = $configPath['path'] . '/' . $key;
            self::deleteFiles($filePath, $fileName);
        }
    }

    public static function moveTrashImageProccess($fileName, $fileTembPath = "", $filePath = "")
    {
        try {
            if ($fileTembPath != "" && $filePath != "" && $fileName != "") {
                $fileTembPath = public_path($fileTembPath . '/' . $fileName);
                $filePath = public_path($filePath . '/' . $fileName);
                if (file_exists($fileTembPath)) {
                    File::move($fileTembPath, $filePath);
                }
                return true;
            }
            return false;
        } catch (Exception $exc) {
            return false;
        }
    }

    public static function resizeImagesProccess($fileName, $fileTembPath = "", $filePath = "", $sizeConfig = "", $thumbPath = "")
    {
        try {
            if ($fileTembPath != "" && $filePath != "" && $sizeConfig != "" && $fileName != "") {
                $size = config('image.sizes.' . $sizeConfig);
                $fileTembPath = public_path($fileTembPath);
                $filePath = public_path($filePath);
                if (!is_dir($filePath)) {
                    mkdir($filePath, 777, true);
                }
                list($width, $height) = $size;
                if (file_exists($fileTembPath . "/" . $fileName)) {
                    (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                        ->pad($width, $height)->toWebp(60)->save($filePath . "/" . $fileName);
                    if ($thumbPath != "") {
                        $thumbPath = public_path($thumbPath);
                        $width = ($width != null) ? $width / 2 : $width;
                        $height = ($height != null) ? $height / 2 : $height;
                        (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                            ->pad($width, $height)->toWebp(60)->save($thumbPath . "/" . $fileName);
                    }
                }
                return true;
            }
            return false;
        } catch (Exception $exc) {
            return false;
        }
    }
    public static function photoProccess($fileName, $fileTembPath = "", $filePath = "", $sizeConfig = "", $thumbPath = "")
    {
        try {
            if ($fileTembPath != "" && $filePath != "" && $sizeConfig != "" && $fileName != "") {
                $size = config('image.sizes.' . $sizeConfig);
                $fileTembPath = public_path($fileTembPath);
                $filePath = public_path($filePath);
                if (!is_dir($filePath)) {
                    mkdir($filePath, 777, true);
                }
                list($width, $height) = $size;
                if (file_exists($fileTembPath . "/" . $fileName)) {
                    (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                        ->resizeDown($width, $height)->toWebp(60)->save($filePath . "/" . $fileName);
                    if ($thumbPath != "") {
                        $thumbPath = public_path($thumbPath);
                        (new \Intervention\Image\ImageManager(new Driver()))->read($fileTembPath . "/" . $fileName)
                            ->pad(120, 120)->toWebp(60)->save($thumbPath . "/" . $fileName);
                    }
                }
                return true;
            }
            return false;
        } catch (Exception $exc) {
            return false;
        }
    }
}
