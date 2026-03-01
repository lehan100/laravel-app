<?php

namespace App\Services;

use Illuminate\Session\SessionManager;

class ProductHit
{
    protected $session;
    protected $instance;
    public function __construct(SessionManager $session)
    {
        $this->session = $session;
        $this->setSessionKey();
    }
    public function setSessionKey($instance = 'product_hit')
    {
        $this->instance = $instance;
    }

    public function getSessionKey()
    {
        return $this->instance;
    }

    public function deleteSession()
    {
        $this->session->flush($this->instance);
    }
    public function getData()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            return $this->session->get($keySession);
        }
        return [];
    }
    public function setData($data)
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            $this->session->put($keySession, $data);
            return true;
        }
        return false;
    }
    public function setViewer($product_id = 0)
    {
        $keySession = $this->getSessionKey();
        $data = $this->getData();
        if (isset($data)) {
            foreach ($data as $val) {
                if ($product_id == $val) {
                    return false;
                }
            }
        }
        $data[] = $product_id;
        $this->session->put($keySession, $data);
        return true;
    }
}
