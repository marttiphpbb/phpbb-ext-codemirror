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

	'ACP_MARTTIPHPBB_CODEMIRROR_VERSION'			=> '<a href="http://codemirror.net">CodeMirror</a> version: %s',
	'ACP_MARTTIPHPBB_CODEMIRROR_THEME'				=> 'Theme',
	'ACP_MARTTIPHPBB_CODEMIRROR_IDENT'				=> 'Ident',
	'ACP_MARTTIPHPBB_CODEMIRROR_ENABLE_LINTING'		=> 'Enable linting',
	'ACP_MARTTIPHPBB_CODEMIRROR_SETTING_SAVED'		=> 'The settings were saved.'
]);
