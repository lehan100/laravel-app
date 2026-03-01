<?php

namespace App\Repositories\Contact;

use App\Repositories\Contact\ContactRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Repositories\EloquentRepository;

class ContactEloquentRepository extends EloquentRepository implements ContactRepositoryInterface
{

    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Contact::class;
    }

    // @Override
    public function listItems($params = null, $options = null)
    {
        $result = null;
        if ($options['task'] == "admin-list-items") {
            $query = $this->_model->select()
                ->orderBy('id', 'asc');
            $result = $query->paginate($params['pagination']['totalItemsPerPage']);
        }
        return $result;
    }

    public function getItem($params = null, $options = null)
    {
        if ($options['task'] == 'get-item') {
            $result = $this->_model
                ->select()
                ->where('id', $params['id'])
                ->first();
            return  $result;
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
        }
        if ($options['task'] == 'change-status') {
            $status = (isset($params['status']) && $params['status'] == 0) ? 1 : 0;
            $result = $this->_model->where('id', $params['id'])->update(['status' => $status]);
        }
        if ($options['task'] == 'add-item') {
            $row = $this->_model;
            $row->name = $params['name'];
            $row->phone = preg_replace('/\s+/', '', $params['phone']);
            $row->email  = $params['email'];
            $row->title  = $params['title'];
            $row->message  = $params['message'];
            $row->save();
            $result = $row->id;
        }
        return ($result > 0) ? TRUE : FALSE;
    }

    // @Override
    public function deleteItem($params = null, $options = null)
    {
    }
}
