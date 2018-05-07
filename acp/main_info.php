<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\acp;

use marttiphpbb\codemirror\util\cnst;

class main_info
{
	function module()
	{
		return [
			'filename'	=> '\marttiphpbb\codemirror\acp\main_module',
			'title'		=> cnst::L_ACP,
			'modes'		=> [			
				'settings'	=> [
					'title'	=> cnst::L_ACP . '_SETTINGS',
					'auth'	=> 'ext_marttiphpbb/codemirror && acl_a_board',
					'cat'	=> [cnst::L_ACP],
				],			
			],
		];
	}
}
