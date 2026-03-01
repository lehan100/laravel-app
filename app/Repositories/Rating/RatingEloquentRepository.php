<?php

namespace App\Repositories\Rating;

use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;

class RatingEloquentRepository extends EloquentRepository implements RatingRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Rating::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select()
                ->with(array('product' => function ($query) {
                    $query->with("url");
                }))
                ->orderBy('id', 'DESC');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == 'frontend-list-item') {
            $query = $this->_model->select('name', 'content', 'images', 'rating', 'is_purchase', 'created_at')
                ->where("product_id", $params['id']);
            if (isset($params['star']) && $params['star'] > 0) {
                $query->where("rating", $params['star']);
            }
            $query->orderBy('id', 'DESC');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        if ($options['task'] == 'frontend-list-item-images') {
            $query = $this->_model->select('name', 'content', 'images', 'rating', 'is_purchase', 'created_at')
                ->whereNotNull("images")
                ->where("product_id", $params['id']);
            $query->orderBy('id', 'DESC');
            $result = $query->get();
        }
        if ($options['task'] == "frontend-list-item-caculator") {
            $result =  $this->_model->select('product_id', DB::raw("SUM(rating) as sum_star"), DB::raw("Count(id) as total_rating"))
                ->where("product_id", $params['id'])
                ->groupBy("product_id")
                ->get();
        }
        if ($options['task'] == "frontend-list-item-with-rating") {
            $result =  $this->_model->select('rating', DB::raw("Count(id) as total_rating"))
                ->where("product_id", $params['id'])
                ->groupBy("rating")
                ->get();
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        $params['phone'] = preg_replace('/\s+/', '', $params['phone']);
        if ($options['task'] == 'get-item') {
            $result = $this->_model
                ->select('id', 'name', 'phone')
                ->where('phone', $params['phone'])
                ->where('product_id', $params['product_id'])
                ->first();
            return ($result) ? TRUE : FALSE;
        }
    }

    // @Override
    public function saveItem($params = null, $options = null)
    {
        $result = 0;
        if ($options['task'] == "admin-update-multi-status") {
            if (isset($params['aid']) && count($params['aid']) > 0) {
                $result = $this->_model->whereIn('id', $params['aid'])->update(['status' => $params['value']]);
            }
            return ($result > 0) ? $result : FALSE;
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
            return ($result > 0) ? $result : FALSE;
        }

        if ($options['task'] == 'add-item') {
            DB::beginTransaction();
            try {
                $row = $this->_model;
                $row->name = $params['name'];
                $row->phone = preg_replace('/\s+/', '', $params['phone']);
                $row->content  = $params['content'];
                $row->images  = $params['images'];
                $row->product_id  = $params['product_id'];
                $row->rating  = $params['rating'];
                $row->is_purchase  = $params['is_purchase'];
                $row->status = isset($params['status']) ? 1 : 0;
                $row->save();
                $id = $row->id;
                event(new \App\Events\RatingResizeImage($params['images']));
                DB::commit();
                return $id;
            } catch (\Exception $e) {
                DB::rollBack();
                return FALSE;
            }
        }
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
    }
}
