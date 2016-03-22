<?php

/** Original source took from https://gist.github.com/asp24/7767888 */

error_reporting(E_ALL);

class CacheFlusher
{
    const APC = 'APC';

    const XCACHE = 'xcache';

    const OP_CACHE = 'opcache';

    /**
     * @param string $varName
     *
     * @return bool
     */
    protected function parseBoolIniSettings($varName)
    {
        $val = ini_get($varName);

        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return string[]
     */
    protected function getEnabledCache()
    {
        $result = [];

        if (extension_loaded('xcache') && $this->parseBoolIniSettings('xcache.cacher')) {
            $result[] = self::XCACHE;
        }

        if ((extension_loaded('opcache') || extension_loaded('Zend OPcache')) &&
            $this->parseBoolIniSettings('opcache.enable')
        ) {
            $result[] = self::OP_CACHE;
        }

        if (extension_loaded('apc') && $this->parseBoolIniSettings('apc.enabled')) {
            $result[] = self::APC;
        }

        return $result;
    }

    protected function flushXCache()
    {
        xcache_clear_cache(XC_TYPE_PHP);
    }

    protected function flushOpCache()
    {
        opcache_reset();
    }

    protected function flushAPCCache()
    {
        apc_clear_cache();
    }

    protected function flushCacheForExtension($extension)
    {
        switch ($extension) {
            case self::APC:
                $this->flushAPCCache();
                break;
            case self::OP_CACHE:
                $this->flushOpCache();
                break;
            case self::XCACHE:
                $this->flushXCache();
                break;
        }
    }

    public function flush()
    {
        clearstatcache(true);

        foreach ($this->getEnabledCache() as $cache) {
            $this->flushCacheForExtension($cache);
        }
    }
}

if ($_SERVER['SERVER_ADDR'] !== '127.0.0.1' || $_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    http_response_code(403);
    exit();
}

if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    http_response_code(405);
    exit();
}

(new CacheFlusher())->flush();
