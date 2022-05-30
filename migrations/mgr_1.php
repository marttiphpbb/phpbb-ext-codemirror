<?php
/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 - 2022 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\migrations;
use marttiphpbb\codemirror\util\cnst;
use marttiphpbb\codemirror\service\store;

class mgr_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\phpbb\db\migration\data\v32x\v321',
		];
	}

	public function update_data()
	{
		$package_json = file_get_contents(__DIR__ . '/../' . cnst::CODEMIRROR_DIR . 'package.json');
		$version = json_decode($package_json, true)['version'];

		$default_config_json = file_get_contents(__DIR__ . '/../default_config.json');

		$data = [
			'version'	=> $version,
			'config'	=> $default_config_json,
		];

		return [
			['config_text.add', [store::KEY, serialize($data)]],

			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				cnst::L_ACP,
			]],

			['module.add', [
				'acp',
				cnst::L_ACP,
				[
					'module_basename'	=> '\marttiphpbb\codemirror\acp\main_module',
					'modes'				=> [
						'config',
					],
				],
			]],
		];
	}
}
