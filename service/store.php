<?php

/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\service;

use phpbb\config\db_text as config_text;
use phpbb\cache\driver\driver_interface as cache;
use marttiphpbb\codemirror\util\cnst;

class store
{
	const KEY = cnst::ID;
	const CACHE_KEY = '_' . self::KEY;

	protected $config_text;
	protected $cache;
	protected $data = [];

	public function __construct(config_text $config_text, cache $cache)
	{
		$this->config_text = $config_text;
		$this->cache = $cache;
	}

	private function load():void
	{
		if ($this->data)
		{
			return;
		}

		$this->data = $this->cache->get(self::CACHE_KEY);

		if ($this->data)
		{
			return;
		}

		$this->data = unserialize($this->config_text->get(self::KEY));
		$this->cache->put(self::CACHE_KEY, $this->data);
	}

	private function write():void
	{
		$this->config_text->set(self::KEY, serialize($this->data));
		$this->cache->put(self::CACHE_KEY, $this->data);
	}

	public function get_all():array
	{
		$this->load();
		return $this->data;
	}

	public function get(string $key):string
	{
		$this->load();
		return $this->data[$key] ?? '';
	}

	public function set(string $key, string $value):void
	{
		$this->load();
		$this->data[$key] = $value;
		$this->write();
	}
}
