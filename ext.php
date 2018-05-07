<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror;

use phpbb\extension\base;

class ext extends base
{
	/**
	 * phpBB 3.2.x and PHP 7+
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return phpbb_version_compare($config['version'], '3.2', '>=') && version_compare(PHP_VERSION, '7', '>=');
	}

	public function enable_step(string $step)
	{
		if ($step == '')
		{
			$store = $this->container->get('marttiphpbb.codemirror.service.store');
			$package_json = file_get_contents(__DIR__ . '/../codemirror/package.json');
			$package_json = json_decode($package_json, true);
			$store->set('version', $package_json['version']);
			return 'get_codemirror_version';
		}
	}
}
