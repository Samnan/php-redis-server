<?php

namespace Clue\Redis\Server\Business;

use Clue\Redis\Server\Storage;
use Clue\Redis\Server\Type;
use Exception;
use InvalidArgumentException;
use Clue\Redis\Server\Client;
use Clue\Redis\Server\InvalidDatatypeException;

class Hashes
{
    private $storage;
    public function __construct(Storage $storage = null)
    {
        if ($storage === null) {
            $storage = new Storage();
        }
        $this->storage = $storage;
    }

    public function hdel($name, $key)
    {
        $hash = $this->storage->getOrCreateHash($name);
        if (!isset($hash[$key])) return 0;
        unset($hash[$key]);
        if ($hash->count() <= 0) $this->storage->unsetKey($name);
        return 1;
    }


    public function hexists($name, $key)
    {
        $hash = $this->storage->getOrCreateHash($name);
        if (!isset($hash[$key])) return 0;
        return 1;
    }

    public function hget($name, $key)
    {
        $hash = $this->storage->getOrCreateHash($name);
        if (!isset($hash[$key])) return null;
        return $hash[$key];
    }

    public function hgetall($name)
    {
        $hash = $this->storage->getOrCreateHash($name);
        $ret = [];
        foreach ($hash as $key => $val) {
            $ret[] = $key;
            $ret[] = $val;
        }
        return $ret;
    }

    public function hincrby($name, $key, $incr)
    {
        $hash = $this->storage->getOrCreateHash($name);
        if (isset($hash[$key])) {
            if ($hash[$key] !== null && !is_numeric($hash[$key])) {
                throw new InvalidDatatypeException('ERR value is not an integer or out of range');
            }
        } else {
            $hash[$key] = 0;
        }
        $hash[$key] += $incr;
        return (int)$hash[$key];
    }

    public function hkeys($name)
    {
        $hash = $this->storage->getOrCreateHash($name);
        $ret = [];
        foreach ($hash as $key => $val) {
            $ret[] = $key;
        }
        return $ret;
    }

    public function hlen($name)
    {
        $hash = $this->storage->getOrCreateHash($name);
        return $hash->count();
    }

    public function hmget($name, ... $keys)
    {
        $hash = $this->storage->getOrCreateHash($name);
        $ret = [];
        foreach ($keys as $key) {
            if (isset($hash[$key])) $ret[] = $hash[$key];
            else $ret[] = null;
        }
        return $ret;
    }

    public function hmset($name, ... $pairs)
    {
        $hash = $this->storage->getOrCreateHash($name);
        while (($key = array_shift($pairs))) {
            $val = array_shift($pairs);
            $hash[$key] = $val;
        }
        return "OK";
    }

    public function hscan($name)
    {
        throw new \RuntimeException("unimplemented");
    }

    public function hset($name, $key, $val)
    {
        $hash = $this->storage->getOrCreateHash($name);
        $hash[$key] = $val;
        return 1;
    }

    public function hsetnx($name, $key, $val)
    {
        $hash = $this->storage->getOrCreateHash($name);
        if (isset($hash[$key])) return 0;
        $hash[$key] = $val;
        return 1;
    }

    public function hstrlen($name, $key)
    {
        $hash = $this->storage->getOrCreateHash($name);
        if (!isset($hash[$key])) return null;
        return strlen($hash[$key]);
    }

    public function hvals($name)
    {
        $hash = $this->storage->getOrCreateHash($name);
        $ret = [];
        foreach ($hash as $val) {
            $ret[] = $val;
        }
        return $ret;
    }
}
