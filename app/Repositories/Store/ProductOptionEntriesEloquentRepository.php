<?php

namespace App\Repositories\Store;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;
use App\Models\ProductOptionAttribute;
use App\Helpers\Price as Price;
use Exception;

class ProductOptionEntriesEloquentRepository extends EloquentRepository implements ProductOptionEntriesInterface
{

    protected $cachePrefix = 'option_entries-';

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProductOptionEntries::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'title',  'type', 'status')
                ->with('attributes')
                ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
            //echo "<pre>";print_r($result);die();
        }
        if ($options['task'] == "admin-list-options") {
            $query = $this->_model->select()
                ->with('attributes')
                ->where("status", 1)
                ->orderBy('id', 'asc');
            $result = $query->get();
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $result = null;

        if ($options['task'] == 'get-item') {
            $result = $this->_model->select()->where('id', $params['id'])
                ->with(array('attributes' => function ($query) {
                    $query->orderBy('sort', 'asc');
                }))
                ->first();
        }
        return $result;
    }

    // @Override
    public function saveItem($params = null, $options = null)
    {
        $result = 0;
        if ($options['task'] == "admin-update-multi-status") {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->update(['status' => $params['value']]);
                (new ProductOptionAttribute())->where("product_entries_id", $params['aid'])->update(['status' => $params['value']]);
            }
            return ($result > 0) ? $result : FALSE;
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
            (new ProductOptionAttribute())->where("product_entries_id", $params['id'])->update(['status' => $status]);
            return ($result > 0) ? $result : FALSE;
        }
        // DB::beginTransaction();
        // try {
        $flag = false;
        if ($options['task'] == "edit-item") {
            $row = $this->_model::where('id', $params['id'])->first();
            $flag = true;
        } elseif ($options['task'] == "add-item") {
            $row = $this->_model;
            $flag = true;
        }
        if ($flag) {
            $row->title = $params['title'];
            $row->type = $params['type'];
            $row->status = isset($params['status']) ? 1 : 0;
            $row->save();
            $id = $row->id;

            $this->saveAttributesSetValue($params, $id);
        }
        //DB::commit();
        return $id;
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return FALSE;
        // }
    }

    public function saveAttributesSetValue($params = null, $id = 0)
    {

        foreach ($params['option_value_name'] as $k => $val) {
            $flag = false;
            if ($params['option_value_task'][$k] == "edit-item") {
                $row = (new ProductOptionAttribute())->where('id', $params['option_value_id'][$k])->first();
                $flag = true;
            } elseif ($params['option_value_task'][$k] == "add-item") {
                $row = new ProductOptionAttribute();
                $flag = true;
            }
            $status = isset($params['option_value_status'][$k]) ? 1 : 0;
            $picture = $params['option_value_picture'][$k];
            $row->product_entries_id = $id;
            $row->title = $val;
            $row->price = Price::getPrice($params['option_value_price'][$k]);
            $row->picture = $picture;
            $row->color = $params['option_value_color'][$k];
            $row->status = (int)$status;
            $row->sort = $k;
            $row->save();
            //Save Picture

            event(new \App\Events\ProductResizeImageOption($picture));

            // if (isset($params['option_value_picture_del']) && count($params['option_value_picture_del']) > 0) {
            //     $pictureOld = $params['option_value_picture_del'];
            //     $pathConfig = config('image.path.option');
            //     foreach ($pictureOld as $pic) {
            //         \App\Helpers\FileUpload::moveTrashImageProccess($pic, $pathConfig['path'], $pathConfig['trash']);
            //     }
            // }
        }
        if (isset($params['task-main']) && $params['task-main'] == 'edit-item') {
            $option_value = $params['option_de_id'];

            if ($option_value != "") {
                $arrIdOptionValue = explode(",", $option_value);
                $this->deleteItems(['option_id' => $arrIdOptionValue], ['task' => 'delete-attribute-value']);
            }
        }
    }

    public function deleteItems($params = null, $options = null)
    {

        DB::beginTransaction();
        try {
            if ($options['task'] == 'delete-attribute-value') {
                if (isset($params['option_id']) && count($params['option_id']) > 0) {
                    $listAttributeSetValue = (new ProductOptionAttribute())->select()->whereIn('id', [$params['option_id']])->get();
                    (new ProductOptionAttribute())->whereIn("id", $params['option_id'])->delete();
                    DB::commit();
                    if (count($listAttributeSetValue) > 0) {
                        event(new \App\Events\ProductTrashImageOption($listAttributeSetValue));
                    }
                }
            }
            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
        DB::beginTransaction();
        try {
            if ($options['task'] == 'delete-item-multi') {
                if (isset($params['aid']) && count($params['aid']) > 0) {
                    $items = $this->_model->select("id")
                        ->with('attributes')
                        ->whereIn('id', $params['aid'])
                        ->get();

                    if (count($items) > 0) {
                        $this->_model->whereIn('id', $params['aid'])->delete();
                        $listOptionValue =  (new ProductOptionAttribute())->select()->whereIn('product_entries_id', $params['aid'])->get();
                        (new ProductOptionAttribute())->whereIn("product_entries_id", $params['aid'])->delete();
                        DB::commit();
                        if (count($listOptionValue) > 0) {
                            event(new \App\Events\ProductTrashImageOption($listOptionValue));
                        }
                    }
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $listOptionValue = (new ProductOptionAttribute())->select()->whereIn('product_entries_id', [$params['id']])->get();
                    $this->_model->where("id", $params['id'])->delete();
                    (new ProductOptionAttribute())->where("product_entries_id", $params['id'])->delete();
                    DB::commit();
                    if (count($listOptionValue) > 0) {
                        event(new \App\Events\AttributeSetTrashImage($listOptionValue));
                    }
                }
            }

            return TRUE;
        } catch (Exception $exc) {
            DB::rollBack();
            return FALSE;
        }
    }
}
