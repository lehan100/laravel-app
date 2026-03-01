<?php

namespace App\Services;

use Illuminate\Session\SessionManager;

class AdminFilterService {

    protected $session;
    protected $instance;

    public function __construct(SessionManager $session) {
        $this->session = $session;
    }

    public function setSessionKey($instance = 'filter') {
        $this->instance = $instance;
    }

    public function getSessionKey() {
        return $this->instance;
    }
    
    public function deleteSession(){
        $this->session->flush($this->instance);
    }

    public function setFilter($params) {
        if (count($params) > 0) {
            $key = $params['key'];
            $value = $params['value'];
            $data = array($key => $value);
            $keySession = $this->getSessionKey();
            if ($this->session->has($keySession)) {
                $data = $this->session->get($keySession);
                if ($value == 0) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $value;
                }
            }
            $this->session->put($keySession, $data);
            return true;
        }
        return false;
    }

    public function getFilter() {
        $keySession = $this->getSessionKey();
        if ($this->session->has($keySession)) {
            return $this->session->get($keySession);
        }
        return [];
    }

}
