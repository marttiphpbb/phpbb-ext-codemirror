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
	=> 'This extension provides a basic integration of CodeMirror for use
		by other extensions. See %1$sCodeMirror configuration%2$s and
		%3$sconfiguration options defined by this extension%4$s
		for all possible options.',
	'ACP_MARTTIPHPBB_CODEMIRROR_VERSION'
	=> '%1$sCodeMirror%2$s version: %3$s',
	'ACP_MARTTIPHPBB_CODEMIRROR_THEME'
	=> 'Theme',
	'ACP_MARTTIPHPBB_CODEMIRROR_TRY_THEME'
	=> 'Try theme',
	'ACP_MARTTIPHPBB_CODEMIRROR_TRY_THEME_EXPLAIN'
	=> 'This selector is just a tool to preview themes;
		it`s setting is not part of the configuration and won`t be saved.
		To change the default theme you can edit or add a "theme"
		in the JSON configuration below.',
	'ACP_MARTTIPHPBB_CODEMIRROR_MODE'
	=> 'Mode',
	'ACP_MARTTIPHPBB_CODEMIRROR_KEYMAP'
	=> 'Key map',
	'ACP_MARTTIPHPBB_CODEMIRROR_CONFIG_SAVED'
	=> 'The configuration was saved.',
	'ACP_MARTTIPHPBB_CODEMIRROR_INVALID_JSON'
	=> 'The JSON configuration contains at least one error.',
	'ACP_MARTTIPHPBB_CODEMIRROR_RESTORE_DEFAULTS'
	=> 'Restore defaults',
	'ACP_MARTTIPHPBB_CODEMIRROR_JSON_ERROR_DEPTH'
	=> 'JSON error: Maximum stack depth exceeded',
	'ACP_MARTTIPHPBB_CODEMIRROR_JSON_ERROR_STATE_MISMATCH'
	=> 'JSON error: Underflow or the modes mismatch',
	'ACP_MARTTIPHPBB_CODEMIRROR_JSON_ERROR_CTRL_CHAR'
	=> 'JSON error: Unexpected control character found',
	'ACP_MARTTIPHPBB_CODEMIRROR_JSON_ERROR_SYNTAX'
	=> 'JSON syntax error, malformed JSON',
	'ACP_MARTTIPHPBB_CODEMIRROR_JSON_ERROR_UTF8'
	=> 'JSON error. Malformed UTF-8 characters, possibly incorrectly encoded.',
	'ACP_MARTTIPHPBB_CODEMIRROR_JSON_ERROR_UNKNOWN'
	=> 'JSON error: unknown error.',
]);
