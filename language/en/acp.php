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
		=> 'See <a href="http://codemirror.net/doc/manual.html#config">
			CodeMirror configuration</a> for the possible options.
			This extension which integrates CodeMirror for use in the ACP 
			by other extensions also',
	'ACP_MARTTIPHPBB_CODEMIRROR_VERSION'			=> '<a href="http://codemirror.net">CodeMirror</a> version: %s',
	'ACP_MARTTIPHPBB_CODEMIRROR_THEME'				=> 'Theme',
	'ACP_MARTTIPHPBB_CODEMIRROR_TRY_THEME'			=> 'Try theme',
	'ACP_MARTTIPHPBB_CODEMIRROR_TRY_THEME_EXPLAIN'	=> 'This is to try other themes. Only "theme" defined in the json configuration below will be saved.',
	'ACP_MARTTIPHPBB_CODEMIRROR_MODE'				=> 'Mode',
	'ACP_MARTTIPHPBB_CODEMIRROR_KEYMAP'				=> 'Key map',
	'ACP_MARTTIPHPBB_CODEMIRROR_CONFIG_SAVED'		=> 'The configuration was saved.',
	'ACP_MARTTIPHPBB_CODEMIRROR_TRY_EXPLAIN'		=> 'Try here the CodeMirror editor in different modes (languages) with the current configuration.',
	'ACP_MARTTIPHPBB_CODEMIRROR_RESTORE_DEFAULTS'	=> 'Restore defaults',
]);
