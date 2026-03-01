<?php

namespace App\Services;

use Illuminate\Session\SessionManager;

class AdminFilter
{
    protected $session;
    protected $instance;
    public function __construct(SessionManager $session)
    {
        $this->session = $session;
        $this->setSessionKey();
    }
    public function setSessionKey($instance = 'admin-filter')
    {
        $this->instance = $instance;
    }

    public function getSessionKey()
    {
        return $this->instance;
    }

    public function deleteSession()
    {
        $this->session->forget($this->instance);
    }
    public function getData()
    {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            return $this->session->get($keySession);
        }
        return null;
    }
    public function setFilter($key,$value)
    {
        $keySession = $this->getSessionKey();
        $data = $this->getData();
        $data[$key] = $value;
        $this->session->put($keySession, $data);
        return $this;
    }
}
