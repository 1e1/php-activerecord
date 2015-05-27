<?php
namespace ActiveRecord;

class Redis
{
    const DEFAULT_PORT = 6379;
    const DEFAULT_TIMEOUT = 0.0;

	private $redis;

	/**
	 * Creates a Redis instance.
	 *
	 * Takes an $options array w/ the following parameters:
	 *
	 * <ul>
	 * <li><b>host:</b> host for the redis server </li>
	 * <li><b>port:</b> port for the redis server </li>
	 * </ul>
	 * @param array $options
	 */
	public function __construct($options)
	{
		$this->redis = new \Redis();
        $options['port'] = isset($options['port']) ? $options['port'] : self::DEFAULT_PORT;
        $options['timeout'] = isset($options['timeout']) ? $options['timeout'] : self::DEFAULT_TIMEOUT;

		if (!$this->redis->connect($options['host'],$options['port'],$options['timeout']))
			throw new CacheException("Could not connect to $options[host]:$options[port]");
	}

	public function flush()
	{
		$this->redis->flushAll();
	}

	public function read($key)
	{
        $serialized = $this->redis->get($key);

        return unserialize($serialized);
	}

	public function write($key, $value, $expire)
	{
        $serialized = serialize($value);
		$this->redis->set($key,$serialized,$expire);
	}

	public function delete($key)
	{
		$this->redis->del($key);
	}
}
