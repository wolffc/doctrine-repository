<?php
namespace Wolffc\DocrineRepository\Cache;

use Doctrine\Common\Cache\Cache;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DoctrineTypo3Cache implements Cache
{

    /**
     * @var FrontendInterface
     */
    protected $cacheFrontend;

    /**
     * Doctrine Namespace
     *
     * @var string
     */
    protected $namespace;


    public function initializeObject()
    {
        $this->cacheFrontend = GeneralUtility::makeInstance(CacheManager::class)->getCache('docrine_repository');
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns a Properly Namespaced id for Typo3
     *
     * @param string $id the id to be namespaced
     *
     * @return string
     */
    public function getNamespacedId($id)
    {
        // remove backslashes and dollar signs
        $id = str_replace(['\\', '$','[',']'], '_', $id);
        return $this->namespace . $id;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id)
    {
        return $this->cacheFrontend->get($this->getNamespacedId($id));
    }
    public function doFetch($id)
    {
        return $this->fetch($id);
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return $this->cacheFrontend->has($this->getNamespacedId($id));
    }

    public function doContains($id)
    {
        return $this->contains($id);
    }

    /**
     * Puts data into the cache.
     *
     * If a cache entry with the given id already exists, its data will be replaced.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->cacheFrontend->set($this->getNamespacedId($id), $data, [], $lifeTime);
        // typo3 does not Provide success state so be optimistic and always return true;
        return true;
    }

    public function doSave($id, $data, $lifeTime = 0)
    {
        return $this->save($id, $data, $lifeTime);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function delete($id)
    {
        return $this->cacheFrontend->remove($this->getNamespacedId($id));
    }

    public function doDelete($id)
    {
        return $this->delete($id);
    }

    /**
     * Retrieves cached information from the data store.
     *
     * The server's statistics array has the following values:
     *
     * - <b>hits</b>
     * Number of keys that have been requested and found present.
     *
     * - <b>misses</b>
     * Number of items that have been requested and not found.
     *
     * - <b>uptime</b>
     * Time that the server is running.
     *
     * - <b>memory_usage</b>
     * Memory used by this server to store items.
     *
     * - <b>memory_available</b>
     * Memory allowed to use for storage.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    public function getStats()
    {
        // Typo3 Cache Api does not Implent Stats calls
        return null;
    }
}
