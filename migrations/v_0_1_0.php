<?php
/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\migrations;
use marttiphpbb\codemirror\util\cnst;
use marttiphpbb\codemirror\service\store;

class v_0_1_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		$package_json = file_get_contents(__DIR__ . '/../codemirror/package.json');
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
