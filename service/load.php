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
	const DEFAULT = [
		'config' => [
			'lineNumbers'	=> true,
			'matchBrackets'	=> true,
			'theme'			=> 'erlang-dark',
		],
		'border' => true,
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

		$version = '5.37.0';

		$load = [
			'themes' 	=> array_keys($this->theme_keys),
			'modes'		=> array_keys($this->mode_keys),
			'keymaps' 	=> array_keys($this->keymap_keys),
		];

		return [
			'mode'				=> $this->mode,
			'theme'				=> $this->theme,
			'data_attr'			=> $this->get_data_attr(),
			'version_param'		=> '?v=' . $version,
			'version'			=> $version,
			'path'				=> $this->get_path(),
			'load'				=> $load,
		];	
	}

	public function get_data_attr():string 
	{
		$data = [
			'config' => [
				'lineNumbers' 	=> true,
				'matchBrackets' => true,
				'theme'			=> $this->theme ?? 'erlang-dark',
				'mode'			=> $this->mode,
			],
			'historyId'	=> 'aaaa',
		];

		$data_attr = ' data-marttiphpbb-codemirror="%s"';
		$data = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
		return sprintf($data_attr, $data);
	}

	public function get_path():string 
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
	}

	public function history_id(string $history_id)
	{
		$this->history_id = $history_id;
	}
}
