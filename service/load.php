<?php

/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\service;

use phpbb\extension\manager as ext_manager;
use marttiphpbb\codemirror\service\store;
use marttiphpbb\codemirror\service\config;
use marttiphpbb\codemirror\service\available;
use marttiphpbb\codemirror\util\cnst;

class load
{
	const MODE_SHORT = [
		'scss'			=> 'text/x-scss',
		'less'			=> 'text/x-less',
		'html'			=> 'text/html',
		'json'			=> 'application/json',
	];

	const MODE_DEP = [
		'dart'				=> 'clike',
		'django'			=> 'htmlmixed',
		'gfm'				=> 'markdown',
		'haml'				=> ['htmlmixed', 'ruby'],
		'haskel-literate'	=> 'haskel',
		'htmlembedded'		=> 'htmlmixed',
		'htmlmixed'			=> ['xml', 'javascript', 'css'],
		'jsx'				=> ['xml', 'javascript'],
		'markdown'			=> 'xml',
		'pegjs'				=> 'javascript',
		'php'				=> ['htmlmixed', 'clike'],
		'pug'				=> ['javascript', 'css', 'htmlmixed'],
		'rst'				=> ['python', 'stex'],
		'sass'				=> 'css',
		'slim'				=> ['htmlmixed', 'ruby'],
		'soy'				=> 'htmlmixed',
		'tornado'			=> 'htmlmixed',
		'vue'				=> ['xml', 'javascript', 'coffeescript', 'css', 'stylus', 'pug', 'handlebars'],
		'yaml-frontmatter'	=> 'yaml',
	];

	const MODE_ADDON_DEP = [
		'django'		=> 'mode/overlay',
		'dockerfile'	=> 'mode/simple',
		'factor'		=> 'mode/simple',
		'gfm'			=> 'mode/overlay',
		'handlebars'	=> ['mode/simple', 'mode/multiplex'],
		'htmlembedded'	=> 'mode/multiplex',
		'nsis'			=> 'mode/simple',
		'rst'			=> 'mode/overlay',
		'rust'			=> 'mode/simple',
		'tornado'		=> 'mode/overlay',
		'twig'			=> 'mode/multiplex',
		'vue'			=> 'mode/overlay',
	];

	const MODE_DEP_META = [
		'markdown'		=> true,
	];

	const ADDON_CSS = [
		'dialog/dialog'				=> true,
		'display/fullscreen'		=> true,
		'fold/foldgutter'			=> true,
		'hint/showhint'				=> true,
		'lint/lint'					=> true,
		'merge/merge'				=> true,
		'scroll/simplescrollbars'	=> true,
		'search/matchesonscrollbar'	=> true,
		'tern/tern'					=> true,
	];

	const ADDON_DEP = [
		'edit/closetag'				=> 'fold/xml-fold',
		'edit/matchtags'			=> 'fold/xml-fold',
		'fold/foldgutter'			=> 'fold/foldcode',
		'hint/html-hint'			=> 'hint/xml-hint',
		'runmode/colorize'			=> 'runmode/runmode',
		'search/jump-to-line'		=> 'dialog/dialog',
		'search/match-highlighter'	=> 'search/matchonscrollbar',
		'search/matchesonscrollbar'	=> ['scroll/annotatescrollbar', 'scroll/simplescrollbars', 'search/searchcursor'],
		'search/search'				=> ['search/searchcursor', 'dialog/dialog'],
	];

	const ADDON_MODE_DEP = [
		'hint/css-hint'		=> 'css',
		'hint/sql-hint'		=> 'sql',
	];

	const ADDON_DEP_EXTRA = [
		'lint/html-lint'	=> 'htmllint',
		'merge/merge'		=> 'diff_match_patch',
	];

	const KEYMAP_ADDON_DEP = [
		'sublime'	=> ['search/searchcursor', 'edit/matchbrackets'],
		'vim'		=> ['search/searchcursor', 'edit/matchbrackets', 'dialog/dialog'],
	];

	const EXT_ADDON_DEP = [
		'fullscreen'	=> [
			'display/fullscreen'
		],
		''
	];

	const COMMAND_ADDON = [
		'',
		'jumpToLine'		=> 'search/jump-to-line',
	];

	const OPTION_ADDON_DEP = [
		'matchBrackets' 		=> 'edit/matchbrackets',
		'autoCloseBrackets'		=> 'edit/closebrackets',
		'matchTags'				=> 'edit/matchtags',
		'showTrailingSpace'		=> 'edit/trailingspace',
		'autoCloseTags'			=> 'edit/closetag',
		'newlineAndIndentContinueMarkdownList'	=> 'edit/continuelist',
		'foldGutter'			=> 'fold/foldgutter',
		'styleActiveLine'		=> 'selection/active-line',
		'continueComments'		=> 'comment/continuecomment',
		'placeholder'			=> 'display/placeholder',
		'fullScreen'			=> 'display/fullscreen',
		'scrollbarStyle'		=> 'scroll/simplescrollbars',
		'rulers'				=> 'display/rulers',
	];

	CONST FROM_COMMANDS = [
		'marttiphpbbToggleFullScreen'	=> 'addon/display/fullscreen',
		'marttiphpbbExitFullScreen'		=> 'addon/display/fullscreen',
	];

	/** @var config */
	private $config;

	/** @var available */
	private $available;

	/** @var string */
	private $phpbb_root_path;

	/** @var string */
	private $ext_root_path;

	private $mode_keys = [];
	private $mode;
	private $theme_keys = [];
	private $theme;
	private $keymap_keys = [];
	private $keymap;
	private $addon_keys = [];

	private $cm_keys = [
		'lib/codemirror.css'	=> true,
		'lib/codemirror.js'		=> true,
	];
	private $ext_keys = [];
	private $custom_keys = [];

	public function __construct(
		config $config,
		available $available,
		string $phpbb_root_path
	)
	{
		$this->config = $config;
		$this->available = $available;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->ext_root_path = $this->phpbb_root_path . cnst::EXT_PATH;		
	}

	public function is_enabled():bool
	{
		return $this->mode ? true : false;
	}

	public function get_listener_data():array 
	{
		if (!$this->mode)
		{
			return [];
		}

		$version = $this->config->get_version();

		$load = [
			'cm'		=> array_keys($this->cm_keys),
			'ext'		=> array_keys($this->ext_keys),
			'custom'	=> array_keys($this->custom_keys),
			'themes' 	=> array_keys($this->theme_keys),
			'modes'		=> array_keys($this->mode_keys),
			'keymaps' 	=> array_keys($this->keymap_keys),
			'addons'	=> array_keys($this->addon_keys),
		];

		return [
			'mode'				=> $this->mode,
			'theme'				=> $this->theme,
			'data_attr'			=> $this->get_data_attr(),
			'cm_version_param'	=> '?v=' . $version,
			'cm_version'		=> $version,
			'cm_path'			=> $this->get_cm_path(),
			'load'				=> $load,
		];	
	}	

	public function get_data_attr():string 
	{
		$data = [
			'config' => [
				'lineNumbers' 	=> true,
				'matchBrackets' => true,
				'extraKeys'		=> [
					'F11'	=> "marttiphpbbToggleFullScreen",
					'Esc'	=> "marttiphpbbExitFullScreen",
					'Ctrl-Alt-B'	=> "marttiphpbbToggleBorder"
				],
				'theme'			=> $this->theme ?? 'night',
				'mode'			=> $this->mode,
			],
			'historyId'	=> 'aaaa',
		];

		$data_attr = ' data-marttiphpbb-codemirror="%s"';
		$data = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
		return sprintf($data_attr, $data);
	}

	public function get_cm_path():string 
	{
		return $this->ext_root_path . cnst::CODEMIRROR_DIR;
	}

	public function select_keymap(string $keymap)
	{
		$this->keymap = $keymap;
		$this->keymap($keymap);
	}

	public function all_keymaps()
	{
		$this->keymaps($this->available->get_keymaps());
	}

	public function keymaps(array $keymaps)
	{
		foreach($keymaps as $keymap)
		{
			$this->keymap($keymap);
		}
	}

	public function keymap(string $keymap)
	{
		$this->keymap_keys[$keymap] = true;
		$this->cm_keys['keymap/' . $keymap . '.js'] = true;
	}

	public function select_mode(string $mode)
	{
		$this->mode = $mode;
		$this->mode($mode);
	}

	public function all_modes()
	{
		$this->modes($this->available->get_modes());
	}

	public function modes(array $modes)
	{
		foreach($modes as $mode)
		{
			$this->mode($mode);
		}
	}

	public function mode(string $mode)
	{
		$this->mode_keys[$mode] = true;
		$this->cm_keys['mode/' . $mode . '/' . $mode . '.js'] = true;
	}

	public function select_theme(string $theme)
	{
		$this->theme = $theme;
		$this->theme($theme);
	}

	public function all_themes()
	{
		$this->themes($this->available->get_themes());
	}

	public function themes(array $themes)
	{
		foreach($themes as $theme)
		{
			$this->theme($theme);
		}
	}

	public function theme(string $theme)
	{
		$this->theme_keys[$theme] = true;
		$this->cm_keys['theme/' . $theme . '.css'] = true;		
	}

	public function addons(array $addons)
	{
		foreach($addons as $addon)
		{
			$this->addon($addon);
		}
	}

	public function addon(string $addon)
	{
		$this->addon_keys[$addon] = true;
		$this->cm_keys['addon/' . $addon . '.js'] = true;

		if (isset(self::ADDON_CSS[$addon]))
		{
			$this->cm_keys['addon/' . $addon . '.css'] = true;
		}
	}

	public function history_id(string $history_id)
	{
		$this->history_id = $history_id;
	}

	public function border()
	{
		$this->border = true;
	}
}
