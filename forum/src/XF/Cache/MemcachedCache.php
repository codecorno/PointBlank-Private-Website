<?php

namespace XF\Cache;

/**
 * This is an extension of the Doctrine Memcached cache provider to fix an issue in the 1.5 version of the library.
 * This issue is fixed in newer versions, but their requirements are higher than ours.
 */
class MemcachedCache extends \Doctrine\Common\Cache\MemcachedCache
{
	/**
     * {@inheritdoc}
     */
    protected function doFetchMultiple(array $keys)
    {
       return parent::doFetchMultiple($keys) ?: [];
    }
}