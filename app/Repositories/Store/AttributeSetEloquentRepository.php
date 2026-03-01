<?php

namespace App\Repositories\Store;

use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\EloquentRepository;
use App\Models\AttributeSetValue;
use App\Models\ProductAttributeSet;
use Exception;
use Illuminate\Support\Facades\URL;
use \Illuminate\Support\Carbon;
class AttributeSetEloquentRepository extends EloquentRepository implements AttributeSetRepositoryInterface
{

    protected $cachePrefix = 'attribute_sets-';

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\AttributeSet::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select('id', 'name', 'alias', 'sort', 'type', 'status')
                ->with('attributes')
                ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == "admin-list-options") {
            $query = $this->_model->select()
                ->with('attributes')
                ->where("status", 1)
                ->orderBy('id', 'asc');
            $result = $query->get();
        }
        if ($options['task'] == "frontend-list-filters") {

            $category_id = isset($params['id_categories']) ? $params['id_categories'] : null;
            $category_id_cache = isset($params['id_categories']) ? Arr::join($category_id, '-') : null;
            $page = isset($params['page']) ? $params['page'] : 0;

            $page = isset($params['page']) ? $params['page'] : 0;
            $strFilter = "";

            if (isset($params['filter_attribute']) && $params['filter_attribute'] != null) {
                foreach ($params['filter_attribute'] as $key => $val) {
                    $strFilter .= $key . Arr::join($val, '-');
                }
            }
            if (isset($params['id_products']) && $params['id_products'] != null) {
                $strFilter .= "products";
            }
            $autopage = isset($params['autopage']) ? $params['autopage'] : false;
            $keyword = isset($params['keyword']) ?  Str::slug($params['keyword']) : '';
            $sale_id = isset($params['sale_id']) ?  $params['sale_id'] : '';
            $dataKeyCache = [
				URL::current(),
                $this->cachePrefix,
                $options['task'],
                $sale_id,
                $strFilter,
                $keyword,
                $params['sort'],
                $category_id_cache,
                $page,
                $autopage
            ];
			
            $keycache = Arr::join($dataKeyCache, "_");
            //$keycache = $this->cachePrefix . $options['task'] . "-" . $category_id_cache . "-page-" . $page;
            $result = Cache::store('custom')->get($keycache, function () use ($params) {
                $query = $this->_model->select();
                if (isset($params['id_products']) && $params['id_products'] != null) {
                    $query->with(array('attributes' => function ($query) use ($params) {
                        // if (isset($params['filter_attribute']) && $params['filter_attribute'] != null) {
                        //     $filter_attribute = $params['filter_attribute'];
                        //     foreach ($filter_attribute as $alias => $attribute) {
                        //         $query->whereIn("id",  $attribute);
                        //     }
                        // }
                        $query->withCount(array("productAttributeSets" => function ($query) use ($params) {
                            $query->whereIn('product_id', $params['id_products']);
                        }));
                    }));
                }

                $query->where("status", 1)
                    ->orderBy('id', 'asc');
                return $query->get();
            });
            if (!Cache::store('custom')->has($keycache)) {
                Cache::store('custom')->put($keycache, $result,Carbon::now()->addMinutes(10));
            }
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
                (new AttributeSetValue())->where("attribute_set_id", $params['aid'])->update(['status' => $params['value']]);
            }
            return ($result > 0) ? $result : FALSE;
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
            (new AttributeSetValue())->where("attribute_set_id", $params['id'])->update(['status' => $status]);
            return ($result > 0) ? $result : FALSE;
        }
        // DB::beginTransaction();
        // try {
        $flag = false;
        if ($options['task'] == "edit-item") {
            $row = $this->_model::where('id', $params['id'])->first();
            $flag = true;
        } elseif ($options['task'] == "add-item") {
            $row = new $this->_model;
            $flag = true;
        }
        if ($flag) {
            $params['attribute_set_alias'] = ($params['attribute_set_alias'] != "") ? $params['attribute_set_alias'] : \App\Helpers\Filter::setUrlKey($params['attribute_set_name']);
            $row->name = $params['attribute_set_name'];
            $row->alias = $params['attribute_set_alias'];
            $row->type = $params['attribute_set_type'];
            $row->status = isset($params['attribute_set_status']) ? 1 : 0;
            $row->save();
            $id = $row->id;

            $this->saveAttributesSetValue($params, $id);
            if (Schema::hasColumn('product_attribute_sets', $params['attribute_set_alias_old']) && $params['attribute_set_alias_old'] != $params['attribute_set_alias']) {
                Schema::table('product_attribute_sets', function (Blueprint $table)  use ($params) {
                    $table->renameColumn($params['attribute_set_alias_old'], $params['attribute_set_alias']);
                });
                (new \App\Models\ProductAttributeSet())->where("alias", "=", $params['attribute_set_alias_old'])->update(['alias' => $params['attribute_set_alias']]);
            } else if (!Schema::hasColumn('product_attribute_sets', $params['attribute_set_alias'])) {
                Schema::table('product_attribute_sets', function (Blueprint $table)  use ($params) {
                    $table->integer($params['attribute_set_alias'])->unsigned()->nullable()->default(0);
                });
            }
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
        foreach ($params['attribute_set_value_name'] as $k => $val) {
            $flag = false;
            if ($params['attribute_set_value_task'][$k] == "edit-item") {
                $row = (new AttributeSetValue())->where('id', $params['attribute_set_value_id'][$k])->first();
                $flag = true;
            } elseif ($params['attribute_set_value_task'][$k] == "add-item") {
                $row = new AttributeSetValue();
                $flag = true;
            }
            $attribute_set_value_alias = \App\Helpers\Filter::setUrlKey($params['attribute_set_value_name'][$k]);
            $row->attribute_set_id = $id;
            $row->name = $val;
            $row->alias = $attribute_set_value_alias;
            $row->picture = $params['attribute_set_value_picture'][$k];
            $row->color = $params['attribute_set_value_color'][$k];
            $row->status = isset($params['attribute_set_value_status'][$k]) ? 1 : 0;
            $row->sort = $k;
            $row->save();
            //Save Picture
            $picture = $params['attribute_set_value_picture'][$k];
            event(new \App\Events\AttributeSetResizeImage($picture));

            if (isset($params['attribute_set_value_picture_del']) && count($params['attribute_set_value_picture_del']) > 0) {
                $pictureOld = $params['attribute_set_value_picture_del'];
                $pathConfig = config('image.path.attribute_set');
                foreach ($pictureOld as $pic) {
                    \App\Helpers\FileUpload::moveTrashImageProccess($pic, $pathConfig['path'], $pathConfig['trash']);
                }
            }
        }
        if (isset($params['task-main']) && $params['task-main'] == 'edit-item') {
            $attribute_set_value = $params['attribute_set_de_id'];

            if ($attribute_set_value != "") {
                $arrIdOptionValue = explode(",", $attribute_set_value);
                $this->deleteItems(['attribute_set_id' => $arrIdOptionValue], ['task' => 'delete-attribute-value']);
            }
        }
    }

    public function deleteItems($params = null, $options = null)
    {

        DB::beginTransaction();
        try {
            if ($options['task'] == 'delete-attribute-value') {
                if (isset($params['attribute_set_id']) && count($params['attribute_set_id']) > 0) {
                    $listAttributeSetValue = (new AttributeSetValue())->select()->whereIn('id', [$params['attribute_set_id']])->get();


                    (new AttributeSetValue())->whereIn("id", $params['attribute_set_id'])->delete();
                    (new ProductAttributeSet())->whereIn("attribute_set_ids", $params['attribute_set_id'])->delete();
                    DB::commit();
                    if (count($listAttributeSetValue) > 0) {
                        event(new \App\Events\AttributeSetTrashImage($listAttributeSetValue));
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
                        $itemsAttributeSet = $this->_model->whereIn('id', $params['aid'])->get();

                        $this->_model->whereIn('id', $params['aid'])->delete();

                        (new AttributeSetValue())->where("attribute_set_id", $params['aid'])->delete();
                        (new ProductAttributeSet())->where("attribute_set_ids", $params['aid'])->delete();
                        foreach ($itemsAttributeSet as $item) {
                            if (Schema::hasColumn('product_attribute_sets', $item['alias'])) {
                                Schema::table('product_attribute_sets', function (Blueprint $table)  use ($item) {
                                    $table->dropColumn($item['alias']);
                                });
                            }
                        }
                        $pathConfig = config('image.path.attribute_set');
                        foreach ($items as $attr) {
                            $attributes = $attr->attributes;
                            foreach ($attributes as $item) {
                                if ($item['picture'] != "") {
                                    \App\Helpers\FileUpload::moveTrashImageProccess($item['picture'], $pathConfig['path'], $pathConfig['trash']);
                                }
                            }
                        }
                    }
                    DB::commit();
                }
            }
            if ($options['task'] == 'delete-item') {
                if (isset($params['id'])) {
                    $itemAttributeSet = $this->_model->where('id', $params['id'])->first();
                    $listAttributeSetValue = (new AttributeSetValue())->select()->whereIn('attribute_set_id', [$params['id']])->get();
                    $this->_model->where("id", $params['id'])->delete();
                    (new AttributeSetValue())->where("attribute_set_id", $params['id'])->delete();
                    DB::commit();
                    if (Schema::hasColumn('product_attribute_sets', $itemAttributeSet['alias'])) {
                        Schema::table('product_attribute_sets', function (Blueprint $table)  use ($itemAttributeSet) {
                            $table->dropColumn($itemAttributeSet['alias']);
                        });
                    }

                    if (count($listAttributeSetValue) > 0) {
                        event(new \App\Events\AttributeSetTrashImage($listAttributeSetValue));
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
