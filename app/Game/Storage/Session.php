<?php
namespace App\Game\Storage;

class Session implements StorageInterface
{

    /**
     * @var \Illuminate\Session\Store
     */
    private $session;

    function __construct(\Illuminate\Session\Store $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getKey($key)
    {
        return $this->session->get($key);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setKey($key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function delete($key)
    {
        return $this->session->pull($key);
    }

}