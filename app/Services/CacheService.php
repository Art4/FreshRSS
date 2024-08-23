<?php
declare(strict_types=1);

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Provide caching functions.
 */
class FreshRSS_Cache_Service implements CacheInterface {

	private string $location;

	private string $extension = 'spc';

	public function __construct(string $location) {
		$this->location = $location;
	}

	/**
	 * Fetches a value from the cache.
	 *
	 * @param string $key     The unique key of this item in the cache.
	 * @param mixed  $default Default value to return if the key does not exist.
	 *
	 * @return mixed The value of the item from the cache, or $default in case of cache miss.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function get(string $key, mixed $default = null): mixed {
		$filepath = $this->createFilepath($key);

		if (file_exists($filepath) && is_readable($filepath)) {
			return unserialize(file_get_contents($filepath));
		}

		return $default;
	}

	/**
	 * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
	 *
	 * @param string                 $key   The key of the item to store.
	 * @param mixed                  $value The value of the item to store, must be serializable.
	 * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
	 *                                      the driver supports TTL then the library may set a default value
	 *                                      for it or let the driver take care of that.
	 *
	 * @return bool True on success and false on failure.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool {
		$filepath = $this->createFilepath($key);

		if (file_exists($filepath) && is_writable($filepath) || file_exists($this->location) && is_writable($this->location)) {
			$data = serialize($value);

			return (bool) file_put_contents($filepath, $data);
		}

		return false;
	}

	/**
	 * Delete an item from the cache by its unique key.
	 *
	 * @param string $key The unique cache key of the item to delete.
	 *
	 * @return bool True if the item was successfully removed. False if there was an error.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function delete(string $key): bool {
		$filepath = $this->createFilepath($key);

		if (file_exists($filepath) && is_readable($filepath)) {
			return unlink(file_get_contents($filepath));
		}

		return false;
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(): bool {
		throw new \Exception(__METHOD__ . 'is not yet implemented');
	}

	/**
	 * Obtains multiple cache items by their unique keys.
	 *
	 * @param iterable<string> $keys    A list of keys that can be obtained in a single operation.
	 * @param mixed            $default Default value to return for keys that do not exist.
	 *
	 * @return iterable<string, mixed> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if $keys is neither an array nor a Traversable,
	 *   or if any of the $keys are not a legal value.
	 */
	public function getMultiple(iterable $keys, mixed $default = null): iterable {
		throw new \Exception(__METHOD__ . 'is not yet implemented');
	}

	/**
	 * Persists a set of key => value pairs in the cache, with an optional TTL.
	 *
	 * @param iterable               $values A list of key => value pairs for a multiple-set operation.
	 * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
	 *                                       the driver supports TTL then the library may set a default value
	 *                                       for it or let the driver take care of that.
	 *
	 * @return bool True on success and false on failure.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if $values is neither an array nor a Traversable,
	 *   or if any of the $values are not a legal value.
	 */
	public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool {
		throw new \Exception(__METHOD__ . 'is not yet implemented');
	}

	/**
	 * Deletes multiple cache items in a single operation.
	 *
	 * @param iterable<string> $keys A list of string-based keys to be deleted.
	 *
	 * @return bool True if the items were successfully removed. False if there was an error.
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if $keys is neither an array nor a Traversable,
	 *   or if any of the $keys are not a legal value.
	 */
	public function deleteMultiple(iterable $keys): bool {
		throw new \Exception(__METHOD__ . 'is not yet implemented');
	}

	/**
	 * Determines whether an item is present in the cache.
	 *
	 * NOTE: It is recommended that has() is only to be used for cache warming type purposes
	 * and not to be used within your live applications operations for get/set, as this method
	 * is subject to a race condition where your has() will return true and immediately after,
	 * another script can remove it making the state of your app out of date.
	 *
	 * @param string $key The cache item key.
	 *
	 * @return bool
	 *
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function has(string $key): bool {
		throw new \Exception(__METHOD__ . 'is not yet implemented');
	}

	private function createFilepath(string $key): string {
		if ($key === '') {
			throw new class (
				'Cache key MUST NOT be an empty string'
			) extends \Exception implements InvalidArgumentException {};
		}

		if (preg_match('#[\{\}\(\)/\\\@\:]#', $key)) {
			throw new class (
				sprintf('Cache key `%s` contains one or more invalid characters: {}()/\@:', $key)
			) extends \Exception implements InvalidArgumentException {};
		}

		return "$this->location/$key.$this->extension";
	}
}
