<?php

/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [

	'ACP_MARTTIPHPBB_CODEMIRROR_CONFIG_EXPLAIN'			
		=> 'This default configuration can be overwritten by the consuming extension. 
			See <a href="http://codemirror.net/doc/manual.html#config">
			CodeMirror configuration</a> for the possible options.',
	'ACP_MARTTIPHPBB_CODEMIRROR_VERSION'			=> '<a href="http://codemirror.net">CodeMirror</a> version: %s',
	'ACP_MARTTIPHPBB_CODEMIRROR_THEME'				=> 'Theme',
	'ACP_MARTTIPHPBB_CODEMIRROR_THEME_EXPLAIN'		=> '"theme" set in the json configuration will overwrite this selection',
	'ACP_MARTTIPHPBB_CODEMIRROR_MODE'				=> 'Mode',
	'ACP_MARTTIPHPBB_CODEMIRROR_KEYMAP'				=> 'Key map',
	'ACP_MARTTIPHPBB_CODEMIRROR_BORDER'				=> 'Add border',
//	'ACP_MARTTIPHPBB_CODEMIRROR_IDENT'				=> 'Ident',
//	'ACP_MARTTIPHPBB_CODEMIRROR_ENABLE_LINTING'		=> 'Enable linting',
	'ACP_MARTTIPHPBB_CODEMIRROR_SETTING_SAVED'		=> 'The settings were saved.'
]);
