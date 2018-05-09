<?php

namespace Inquirer;

use Inquirer\Exception\StorageException;

class Storage
{
    private $path;
    private $data;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @throws StorageException
     * @return mixed;
     */
    public function get()
    {
        if (is_null($this->data)) {
            $this->data = $this->getData();
        }
        return $this->data;
    }

    /**
     * @param mixed $data
     * @throws StorageException
     */
    public function set($data)
    {
        $rawData = json_encode($data);
        if (false === $rawData) {
            throw new StorageException("Unable to encode data to store it at '{$this->path}'");
        }
        $result = file_put_contents($this->path, $rawData);
        if (false === $result) {
            throw new StorageException("Unable to put data to storage at '{$this->path}'");
        }
        $this->data = $data;
    }

    /**\
     * @param $key
     * @param $value
     * @throws StorageException
     */
    public function add($key, $value)
    {
        $data = $this->get();
        if (!($data instanceof \stdClass)) {
            $dataType = gettype($data);
            throw new StorageException("Add operation not available for '{$dataType}' data type");
        }
        if (isset($data->$key)) {
            throw new StorageException("Key '{$key}' already exists");
        }
        $data->$key = $value;
        $this->set($data);
    }

    /**
     * @param $key
     * @param $value
     * @throws StorageException
     */
    public function replace($key, $value)
    {
        $data = $this->get();
        if (!($data instanceof \stdClass)) {
            $dataType = gettype($data);
            throw new StorageException("Add operation not available for '{$dataType}' data type");
        }
        $data->$key = $value;
        $this->set($data);
    }

    /**
     * @return mixed
     * @throws StorageException
     */
    private function getData()
    {
        if (!file_exists($this->path)) {
            return new \stdClass();
        }
        $rawData = file_get_contents($this->path);
        if (false === $rawData) {
            throw new StorageException("Unable to get data from storage at '{$this->path}'");
        }
        $data = json_decode($rawData);
        $errorCode = json_last_error();
        if (is_null($data) && JSON_ERROR_NONE != $errorCode) {
            throw new StorageException("Unable to decode data from storage at '{$this->path}', error code is '{$errorCode}'");
        }
        return $data;
    }
}
