<?php

/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\service;

use phpbb\extension\manager as ext_manager;
use marttiphpbb\codemirror\service\store;
use marttiphpbb\codemirror\util\cnst;
use marttiphpbb\codemirror\util\dependencies as dep;

class load
{
	/** @var store */
	private $store;

	/** @var string */
	private $phpbb_root_path;

	/** @var string */
	private $ext_root_path;

	private $cm_css = [];
	private $cm_js = [];
	private $ext_css = [];
	private $ext_js = [];
	private $custom_css = [];
	private $custom_js = [];
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
		store $store,
		string $phpbb_root_path
	)
	{
		$this->store = $store;
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
			'cm_css'		=> array_keys($this->cm_css),
			'cm_js'			=> array_keys($this->cm_js),
			'ext_css'		=> array_keys($this->ext_css),
			'ext_js'		=> array_keys($this->ext_js),
			'cm'			=> array_keys($this->cm_keys),
			'ext'			=> array_keys($this->ext_keys),
			'custom_css'	=> array_keys($this->custom_keys),
			'custom_js'		=> array_keys($this->custom_js),
			'themes' 		=> array_keys($this->theme_keys),
			'modes'			=> array_keys($this->mode_keys),
			'keymaps' 		=> array_keys($this->keymap_keys),
			'addons'		=> array_keys($this->addon_keys),
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
		$this->keymaps(dep::KEYMAPS);
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
		$this->cm_js[dep::KEYMAPS[$keymap]] = true;
		$this->keymap_keys[$keymap] = true;
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
		$this->cm_js[dep::MODES[$mode]] = $mode;
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
		$this->cm_css[dep::THEMES[$theme]] = $theme;		
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

//		$this->cm_js[dep::]

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
