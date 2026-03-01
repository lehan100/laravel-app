<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
  | Route::get('/',  ['as'=>'user',    'uses'=>'UserController@index']);
  | @ as: Tên route tự đặt
  | @ uses: Controller view
  |
 */

Route::post('image-upload', [App\Http\Controllers\ImageUploadController::class, 'storeImage'])->name('image.upload');
Route::post('product-upload', [App\Http\Controllers\ImageUploadController::class, 'storeProduct'])->name('product.upload');
Route::post('product-delete', [App\Http\Controllers\ImageUploadController::class, 'storeProductDelete'])->name('product.delete');
Route::post('product-option', [App\Http\Controllers\ImageUploadController::class, 'storeProductOption'])->name('product.option');
Route::post('rating-upload', [App\Http\Controllers\ImageUploadController::class, 'storeProductRating'])->name('rating.upload');
Route::post('product-option-delete', [App\Http\Controllers\ImageUploadController::class, 'storeProductOptionDelete'])->name('product.option.delete');
Route::post('attribute-option', [App\Http\Controllers\ImageUploadController::class, 'storeAttributeOption'])->name('attribute.option');
Route::post('attribute-option-delete', [App\Http\Controllers\ImageUploadController::class, 'storeAttributeOptionDelete'])->name('attribute.option.delete');
Route::post('category-upload', [App\Http\Controllers\ImageUploadController::class, 'storeCategory'])->name('category.upload');
Route::post('category-delete', [App\Http\Controllers\ImageUploadController::class, 'storeCategoryDelete'])->name('category.delete');
Route::post('post-upload', [App\Http\Controllers\ImageUploadController::class, 'storePost'])->name('post.upload');
Route::post('post-delete', [App\Http\Controllers\ImageUploadController::class, 'storePostDelete'])->name('post.delete');
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
    ->name('ckfinder_connector');

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
    ->name('ckfinder_browser');
$prefixAdmin = config('configs.prefix.admin');
Route::group(['prefix' => $prefixAdmin, 'namespace' => 'Admin'], function () {
    Route::get('/', function () {
        return redirect()->route('auth/login');
    });
    /* -----------LOGIN--------------- */
    $prefix = 'auth';
    $controllerName = 'auth';
    Route::group(['prefix' => $prefix], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/login', ['as' => $controllerName . '/login', 'uses' => $controller . 'login']);
        Route::post('/post-login', ['as' => $controllerName . "/post-login", 'uses' => $controller . 'postlogin']);
        Route::get('/logout', ['as' => $controllerName . '/logout', 'uses' => $controller . 'logout']);
    });
    /* -----------Dashboard--------------- */
    $prefix = 'dashboard';
    $controllerName = 'dashboard';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
    });
    /* -----------Config--------------- */
    $prefix = 'settings';
    $controllerName = 'settings';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('/generate-sitemap', ['as' => $controllerName . '/sitemap', 'uses' => $controller . 'sitemap']);
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
    });
    /* -----------Province/City--------------- */
    $prefix = 'province';
    $controllerName = 'province';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------District--------------- */
    $prefix = 'district';
    $controllerName = 'district';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']);
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------Ward--------------- */
    $prefix = 'ward';
    $controllerName = 'ward';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']);
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------USER--------------- */
    $prefix = 'user';
    $controllerName = 'user';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------CATEGORY--------------- */
    $prefix = 'category';
    $controllerName = 'category';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/{id?}', ['as' => $controllerName, 'uses' => $controller . 'index'])->where('id', '[0-9]+');
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('form-ajax/{id?}', ['as' => $controllerName . '/formajax', 'uses' => $controller . 'formajax'])->where('id', '[0-9]+');
        Route::get('category-select', ['as' => $controllerName . '/categorySelect', 'uses' => $controller . 'categorySelect']);
        Route::post('get-items', ['as' => $controllerName . '/getItemsCategory', 'uses' => $controller . 'getItemsCategory']);
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::post('sort', ['as' => $controllerName . '/sort', 'uses' => $controller . 'sort'])->middleware("cache.flush");
        Route::get('delete/{id?}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->middleware("cache.flush")->where('id', '[0-9]+');
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------PRODUCT--------------- */
    $prefix = 'product';
    $controllerName = 'product';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::post('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']); 
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
        Route::get('option', ['as' => $controllerName . '/option', 'uses' => $controller . 'option']);
        Route::get('option-attribute', ['as' => $controllerName . '/attribute', 'uses' => $controller . 'attribute']);
        Route::get('option-value', ['as' => $controllerName . '/value', 'uses' => $controller . 'value']);
        Route::get('product-select', ['as' => $controllerName . '/productSelect', 'uses' => $controller . 'productSelect']);
        Route::post('get-items', ['as' => $controllerName . '/getItemsProduct', 'uses' => $controller . 'getItemsProduct']);
        Route::get('option-entries', ['as' => $controllerName . '/optionEntries', 'uses' => $controller . 'optionEntries']);
    });
    /* -----------PRODUCT ATTRIBUTE--------------- */
    $prefix = 'attribute';
    $controllerName = 'attribute';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
        Route::get('option', ['as' => $controllerName . '/option', 'uses' => $controller . 'option']);
        Route::get('option-attribute', ['as' => $controllerName . '/attribute', 'uses' => $controller . 'attribute']);
        Route::get('option-value', ['as' => $controllerName . '/value', 'uses' => $controller . 'value']);
    });
    /* -----------PRODUCT option entries--------------- */
   $prefix = 'option_entries';
     $controllerName = 'optionEntries';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
        Route::get('option', ['as' => $controllerName . '/option', 'uses' => $controller . 'option']);
        Route::get('option-attribute', ['as' => $controllerName . '/attribute', 'uses' => $controller . 'attribute']);
        Route::get('option-value', ['as' => $controllerName . '/value', 'uses' => $controller . 'value']);
    });
    /* -----------Rating--------------- */
    $prefix = 'rating';
    $controllerName = 'rating';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------Inventory--------------- */
    $prefix = 'inventory';
    $controllerName = 'inventory';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('history/{id?}', ['as' => $controllerName . '/history', 'uses' => $controller . 'history'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::post('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']);
    });
    /* -----------POST--------------- */
    $prefix = 'post';
    $controllerName = 'post';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
        Route::get('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']);
    });
    /* -----------Contact--------------- */
    $prefix = 'contact';
    $controllerName = 'contact';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------Media/Position--------------- */
    $prefix = 'media/position';
    $controllerName = 'position';
    Route::group(['prefix' => $prefix, 'namespace' => 'Media', 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------Media/Photo--------------- */
    $prefix = 'media/photo';
    $controllerName = 'photo';
    Route::group(['prefix' => $prefix, 'namespace' => 'Media', 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']);
        Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
        Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
        Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
    });
    /* -----------POST--------------- */
    $prefix = 'order';
    $controllerName = 'order';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('view/{id?}', ['as' => $controllerName . '/view', 'uses' => $controller . 'view'])->where('id', '[0-9]+');
        Route::post('post-invoice', ['as' => $controllerName . '/post-invoice', 'uses' => $controller . 'postInvoice']);
        Route::post('post-shipping/{type?}', ['as' => $controllerName . '/post-shipping', 'uses' => $controller . 'postShipping'])->where('type', '[0-9A-Za-z]+');
        Route::post('post-payment', ['as' => $controllerName . '/post-payment', 'uses' => $controller . 'postPayment'])->where('id', '[0-9]+');
        Route::post('post-timeline', ['as' => $controllerName . '/post-timeline', 'uses' => $controller . 'postTimeline'])->where('id', '[0-9]+');
        //Route::get('cancel/{id}', ['as' => $controllerName . '/cancel', 'uses' => $controller . 'cancel'])->where('id', '[0-9]+')->middleware("cache.flush");
        //Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
        Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
        Route::post('filter', ['as' => $controllerName . '/filter', 'uses' => $controller . 'filter']);
    });
     /* -----------Coupon Codes--------------- */
     $prefix = 'coupon';
     $controllerName = 'coupon';
     Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
         $controller = ucfirst($controllerName) . 'Controller@';
         Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
         Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
         Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
         Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
         Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
         Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
     });
     /* -----------Sales--------------- */
     $prefix = 'sales';
     $controllerName = 'sales';
     Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
         $controller = ucfirst($controllerName) . 'Controller@';
         Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
         Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
         Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
         Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
         Route::get('get-list', ['as' => $controllerName . '/listSales', 'uses' => $controller . 'listSales'])->where('id', '[0-9]+');
         Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
         Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
     });
    /* -----------Cache--------------- */
    $prefix = 'cache';
    $controllerName = 'cache';
    Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
        Route::get('refresh/{type}', ['as' => $controllerName . '/refresh', 'uses' => $controller . 'refresh']);
        Route::get('queue', ['as' => $controllerName . '/queue', 'uses' => $controller . 'queue']);
        Route::get('flush', ['as' => $controllerName . '/flush', 'uses' => $controller . 'flush']);
        Route::get('image', ['as' => $controllerName . '/image', 'uses' => $controller . 'image']);
        Route::get('create-permission', ['as' => $controllerName . '/permission', 'uses' => $controller . 'permission']);
    });
    Route::resource('roles', App\Http\Controllers\Admin\RolesController::class);
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionsController::class);
    /* -----------Tier Price--------------- */
     $prefix = 'tier-price';
     $controllerName = 'tierPrice';
     Route::group(['prefix' => $prefix, 'middleware' => ['check.login', 'auth', 'permission']], function () use ($controllerName) {
         $controller = ucfirst($controllerName) . 'Controller@';
         Route::get('/', ['as' => $controllerName, 'uses' => $controller . 'index']);
         Route::get('form/{id?}', ['as' => $controllerName . '/form', 'uses' => $controller . 'form'])->where('id', '[0-9]+');
         Route::post('save', ['as' => $controllerName . '/save', 'uses' => $controller . 'save'])->middleware("cache.flush");
         Route::get('delete/{id}', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9]+')->middleware("cache.flush");
         Route::get('status/{id}/{status}', ['as' => $controllerName . '/status', 'uses' => $controller . 'status'])->where('id', '[0-9]+')->middleware("cache.flush");
         Route::post('multiple-update/{type}', ['as' => $controllerName . '/multiple', 'uses' => $controller . 'multiple'])->middleware("cache.flush");
         Route::get('get-options', ['as' => $controllerName . '/listOptions', 'uses' => $controller . 'listOptions'])->where('id', '[0-9]+');
         Route::get('get-products', ['as' => $controllerName . '/listProducts', 'uses' => $controller . 'listProducts'])->where('id', '[0-9]+');
     });
});

$prefixAdmin = config('configs.prefix.frontend');
Route::group(['prefix' => $prefixAdmin, 'namespace' => 'Default'], function () {
    /* -----------HOMEPAGE--------------- */
    $prefix = '';
    $controllerName = 'home';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/', ['as' => $controllerName . '/index', 'uses' => $controller . 'index']);
    });
    $prefix = 'news';
    $controllerName = 'news';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/list/{id}', ['as' => $controllerName . '/list', 'uses' => $controller . 'list'])->where('id', '[0-9]+');
        Route::get('/detail/{id}', ['as' => $controllerName . '/detail', 'uses' => $controller . 'detail'])->where('id', '[0-9]+');
        Route::get('/view/{id}', ['as' => $controllerName . '/view', 'uses' => $controller . 'view'])->where('id', '[0-9]+');
        Route::get('/viewer', ['as' => $controllerName . '/viewer', 'uses' => $controller . 'viewer'])->where('id', '[0-9]+');
    });
    $prefix = 'contact';
    $controllerName = 'contact';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/index/{id}', ['as' => $controllerName . '/index', 'uses' => $controller . 'index'])->where('id', '[0-9]+');
        Route::post('/post-contact', ['as' => $controllerName . '/post-contact', 'uses' => $controller . 'post']);
    });
    $prefix = 'product';
    $controllerName = 'product';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/list/{id}', ['as' => $controllerName . '/list', 'uses' => $controller . 'list'])->where('id', '[0-9]+');
        Route::get('/sale/{id}', ['as' => $controllerName . '/sale', 'uses' => $controller . 'sale'])->where('id', '[0-9]+');
        Route::get('/view/{id}', ['as' => $controllerName . '/view', 'uses' => $controller . 'view'])->where('id', '[0-9]+');
        Route::get('/viewer', ['as' => $controllerName . '/viewer', 'uses' => $controller . 'viewer'])->where('id', '[0-9]+');
        Route::post('/ajax-search', ['as' => $controllerName . '/ajax-search', 'uses' => $controller . 'ajaxSearch'])->where('keyword', '[0-9A-Za-z]+');
        Route::get('/search/term/{query_text?}', ['as' => $controllerName . '/search/term', 'uses' => $controller . 'searchTerm'])->where('query_text', '[a-z0-9A-Z _ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễếệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ]+');
        Route::get('/search/{keyword?}', ['as' => $controllerName . '/search', 'uses' => $controller . 'search'])->where('keyword', '[a-z0-9A-Z _ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễếệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ]+');
        Route::post('/post-ranting', ['as' => $controllerName . '/post-ranting', 'uses' => $controller . 'postRanting'])->where('product_id', '[0-9]+')->middleware("cache.flush");
        Route::get('/rating', ['as' => $controllerName . '/rating', 'uses' => $controller . 'Rating'])->where('product_id', '[0-9]+');
        Route::get('/rating-image', ['as' => $controllerName . '/ratingImage', 'uses' => $controller . 'ratingImage'])->where('product_id', '[0-9]+');
    });
    $prefix = 'cart';
    $controllerName = 'cart';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::post('/add-cart', ['as' => $controllerName . '/add-cart', 'uses' => $controller . 'addCart'])->where('id', '[0-9]+');
        Route::get('/minicart', ['as' => $controllerName . '/minicart', 'uses' => $controller . 'miniCart']);
        Route::post('/update', ['as' => $controllerName . '/update', 'uses' => $controller . 'update'])->where('id', '[0-9A-Za-z]+');
        Route::post('/delete', ['as' => $controllerName . '/delete', 'uses' => $controller . 'delete'])->where('id', '[0-9A-Za-z]+');
    });
    $prefix = 'province';
    $controllerName = 'province';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/get-city', ['as' => $controllerName . '/get-city', 'uses' => $controller . 'getCity']);
        Route::get('/get-district', ['as' => $controllerName . '/get-district', 'uses' => $controller . 'getDistrict'])->where('id', '[0-9]+');
        Route::get('/get-ward', ['as' => $controllerName . '/get-ward', 'uses' => $controller . 'getWard'])->where('id', '[0-9]+');
    });
    $prefix = 'checkout';
    $controllerName = 'checkout';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::get('/cart', ['as' => $controllerName . '/cart', 'uses' => $controller . 'cart']);
        Route::post('/cart/posts', ['as' => $controllerName . '/posts', 'uses' => $controller . 'posts']);
        Route::get('/cart-empty', ['as' => $controllerName . '/cart-empty', 'uses' => $controller . 'cartEmpty']);
        Route::get('/payment', ['as' => $controllerName . '/payment', 'uses' => $controller . 'payment']);
        Route::get('/get-shipping', ['as' => $controllerName . '/shipping', 'uses' => $controller . 'shipping']);
        Route::post('/post-order', ['as' => $controllerName . '/order', 'uses' => $controller . 'order']);
        Route::get('/success', ['as' => $controllerName . '/success', 'uses' => $controller . 'success']);
    });
    $prefix = 'coupon';
    $controllerName = 'coupon';
    Route::group(['prefix' => $prefix], function ($a) use ($controllerName) {
        $controller = ucfirst($controllerName) . 'Controller@';
        Route::post('/verify', ['as' => $controllerName . '/verify', 'uses' => $controller . 'verify']);
    });
});

Route::get('/{any}', function ($any, Request $mainRequest) {
    $queryString = $mainRequest->getQueryString();
    $tbUrlRewrite = new App\Models\UrlRewrite();
    $urlRewrite = $tbUrlRewrite->select("route")->where("path", $any)->first();
    if ($urlRewrite) {
        $routeLink = ($urlRewrite->route != "") ? $urlRewrite->route : abort(404);
        $url = asset($routeLink);
        if ($queryString != "") {
            $url .= "?" . $queryString;
        }
        $request = $mainRequest::create($url, 'GET');
        $response = app()->handle($request);
        $responseBody = $response->getContent();
        if ($responseBody == "") {
            abort(404);
        }
        echo $responseBody;
    } else {
        abort(404);
    }
})->where('any', '.*');
