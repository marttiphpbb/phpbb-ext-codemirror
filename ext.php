<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 - 2022 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror;

use phpbb\extension\base;
use marttiphpbb\codemirror\service\store;
use marttiphpbb\codemirror\util\cnst;

class ext extends base
{
	public function is_enableable()
	{
		$config = $this->container->get('config');
		return phpbb_version_compare($config['version'], '3.3.0', '>=')
			&& version_compare(PHP_VERSION, '7.1', '>=');
	}

	public function enable_step($old_step)
	{
		if ($old_step === false)
		{
			$config_text = $this->container->get('config_text');
			$data = $config_text->get(store::KEY);

			if ($data)
			{
				// if no data exists, version setting is handled by migration.

				$package_json = file_get_contents(__DIR__ . '/' . cnst::CODEMIRROR_DIR . 'package.json');
				$version = json_decode($package_json, true)['version'];
				$data = serialize(array_merge(unserialize($data), ['version' => $version]));
				$config_text->set(store::KEY, $data);
			}

			return 'codemirror_version_set';
		}

        return parent::enable_step($old_step);
	}
}
