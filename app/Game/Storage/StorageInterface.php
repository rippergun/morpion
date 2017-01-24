<?php
namespace App\Game\Storage;

interface StorageInterface {

    /**
     * @param string $key
     * @return mixed
     */
    public function getKey($key);

    /**
     * @param string $key
     * @param string $value
     */
    public function setKey($key, $value);

    /**
     * @param $key
     * @return mixed
     */
    public function delete($key);

}