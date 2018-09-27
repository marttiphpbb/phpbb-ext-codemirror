<?php
/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\util;

class cnst
{
	const FOLDER = 'marttiphpbb/codemirror';
	const ID = 'marttiphpbb_codemirror';
	const PREFIX = self::ID . '_';
	const L = 'MARTTIPHPBB_CODEMIRROR';
	const L_ACP = 'ACP_' . self::L;
	const L_MCP = 'MCP_' . self::L;
	const TPL = '@' . self::ID . '/';
	const EXT_PATH = 'ext/' . self::FOLDER . '/';
	const CODEMIRROR_DIR = 'node_modules/codemirror/';
	const LIB_DIR = self::CODEMIRROR_DIR . 'lib/';
	const THEME_DIR = self::CODEMIRROR_DIR . 'theme/';
	const MODE_DIR = self::CODEMIRROR_DIR . 'mode/';
	const KEYMAP_DIR = self::CODEMIRROR_DIR . 'keymap/';
	const ADDON_DIR = self::CODEMIRROR_DIR . 'addon/';
}
