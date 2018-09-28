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
	protected $store;
	protected $phpbb_root_path;
	protected $ext_root_path;
	protected $cm_css = [
		'lib/codemirror'	=> true,
	];
	protected $cm_js = [
		'lib/codemirror'	=> true,
	];
	protected $ext_css = [];
	protected $ext_js = [];
	protected $custom_css = [];
	protected $custom_js = [];
	protected $mode_keys = [];
	protected $theme_keys = [
		'default'	=> true,
	];
	protected $keymap_keys = [
		'default'	=> true,
	];
	protected $addon_keys = [];
	protected $config;
	protected $valid_config = true;
	protected $version;
	protected $enabled = false;
	protected $history_id;
	protected $default_content;

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
		return $this->enabled;
	}

	private function load_data()
	{
		if (!isset($this->version))
		{
			$data = $this->store->get_all();
			$this->version = $data['version'];
			$config = $data['config'];
			$this->config = json_decode($config, true);

			if (!isset($this->config))
			{
				$this->valid_config = false;
				$config = file_get_contents(__DIR__ . '/../default_config.json');
				$this->config = json_decode($config, true);
			}
		}
	}

	public function set_option(string $option, $value)
	{
		$this->load_data();
		$this->config[$option] = $value;
	}

	public function get_option(string $option)
	{
		$this->load_data();
		return $this->config[$option] ?? null;
	}

	public function get_listener_data():array
	{
		if (!$this->enabled)
		{
			return [];
		}

		foreach ($this->config as $option => $value)
		{
			if ($option === 'theme' && isset(dep::THEMES[$value]))
			{
				$theme = $value;
				$this->theme_keys[$value] = true;
				$this->cm_css[dep::THEMES[$value]] = true;
				continue;
			}

			if ($option === 'mode')
			{
				$mode = $value;

				if (isset(dep::MODES[$value]))
				{
					$this->mode_keys[$value] = true;
					$this->cm_js[dep::MODES[$value]] = true;
					continue;
				}

				if (isset(dep::MIMES[$value]))
				{
					$this->mode_keys[$value] = true;
					$this->cm_js[dep::MODES[dep::MIMES[$value]]] = true;
					continue;
				}
			}

			if ($option === 'keyMap' && isset(dep::KEYMAPS[$value]))
			{
				$keymap = $value;
				$this->keymap_keys[$value] = true;
				$this->cm_js[dep::KEYMAPS[$value]] = true;

				continue;
			}

			if ($option === 'extraKeys')
			{
				foreach ($value as $key => $command)
				{
					if (isset(dep::COMMANDS[$command]))
					{
						$this->cm_js[dep::COMMANDS[$command]] = true;
					}

					if (isset(dep::EXT_COMMANDS[$command]))
					{
						$this->ext_js[dep::EXT_COMMANDS[$command]] = true;
					}
				}

				continue;
			}

			if ($option === 'showTrailingSpace' && $value === true)
			{
				$this->ext_css['css/trailingspace'] = true;
			}

			if (isset(dep::OPTIONS[$option]))
			{
				$this->cm_js[dep::OPTIONS[$option]] = true;
				continue;
			}

			if (isset(dep::EXT_OPTIONS[$option]))
			{
				$this->ext_js[dep::EXT_OPTIONS[$option]] = true;
				continue;
			}

			if ($option === 'colorpicker')
			{
				$colorpicker_base = $this->ext_root_path . cnst::COLORPICKER_DIR;
				$colorpicker_base .= 'dist/codemirror-colorpicker';
				$this->custom_css[$colorpicker_base . '.css'] = true;
				$this->custom_js[$colorpicker_base . '.min.js'] = true;
			}
		}

		foreach ($this->ext_js as $file => $b)
		{
			if (isset(dep::EXT_USE_OPTIONS[$file]))
			{
				foreach (dep::EXT_USE_OPTIONS[$file] as $option)
				{
					if (isset(dep::OPTIONS[$option]))
					{
						$this->cm_js[dep::OPTIONS[$option]] = true;
						continue;
					}

					if (isset(dep::EXT_OPTIONS[$option]))
					{
						$this->ext_js[dep::EXT_OPTIONS[$option]] = true;
						continue;
					}
				}
			}
		}

		foreach ($this->cm_js as $file => $b)
		{
			$this->load_cm_file_dep($file);
		}

		foreach ($this->cm_js as $file => $b)
		{
			if (isset(dep::CSS[$file]))
			{
				$this->cm_css[$file] = true;
			}
		}

		foreach ($this->ext_js as $file => $b)
		{
			if (isset(dep::EXT_CSS[$file]))
			{
				$this->ext_css[dep::EXT_CSS[$file]] = true;
			}
		}

		$data = [
			'config'	=> $this->config,
		];

		if (isset($this->history_id))
		{
			$data['historyId'] = $this->history_id;
		}

		if (isset($this->default_content))
		{
			$data['defaultContent'] = $this->default_content;
		}

		$data_attr = ' data-marttiphpbb-codemirror="%s"';
		$data = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
		$data_attr = sprintf($data_attr, $data);

		$load = [
			'cm_css'		=> array_keys($this->cm_css),
			'cm_js'			=> array_keys($this->cm_js),
			'ext_css'		=> array_keys($this->ext_css),
			'ext_js'		=> array_keys($this->ext_js),
			'custom_css'	=> array_keys($this->custom_css),
			'custom_js'		=> array_keys($this->custom_js),
		];

		return [
			'mode'				=> $mode,
			'modes'				=> array_keys($this->mode_keys),
			'theme'				=> $theme ?? 'default',
			'themes'			=> array_keys($this->theme_keys),
			'keymap'			=> $keymap ?? 'default',
			'keymaps'			=> array_keys($this->keymap_keys),
			'data_attr'			=> $data_attr,
			'cm_version_param'	=> '?v=' . $this->version,
			'cm_version'		=> $this->version,
			'cm_path'			=> $this->ext_root_path . cnst::CODEMIRROR_DIR,
			'valid_config'		=> $this->valid_config,
			'load'				=> $load,
		];
	}

	private function load_cm_file_dep(string $file)
	{
		if (isset(dep::FILES[$file]))
		{
			foreach(dep::FILES[$file] as $f)
			{
				$this->cm_js[$f] = true;
				$this->load_cm_file_dep($f);
			}
		}
	}

	public function set_extra_key(string $key, string $command)
	{
		$extra_keys = $this->get_option('extraKeys');
		$extra_keys[$key] = $command;
		$this->set_option('extraKeys', $extra_keys);
	}

	public function get_extra_key(string $key)
	{
		return $this->get_option('extraKeys')[$key] ?? null;
	}

	public function set_theme(string $theme)
	{
		$this->set_option('theme', $theme);
	}

	public function get_theme(string $theme):string
	{
		return $this->get_option($theme) ?? 'default';
	}

	public function set_mode(string $mode)
	{
		if (isset(dep::MODES[$mode]))
		{
			$this->set_option('mode', $mode);
			$this->enabled = true;
			return;
		}

		if (isset(dep::MIMES[$mode]))
		{
			$this->set_option('mode', $mode);
			$this->enabled = true;
			return;
		}

		if (isset(dep::NAMES_TO_MIMES[$mode]))
		{
			$mode = dep::NAMES_TO_MIMES[$mode][0];
			$this->set_option('mode', $mode);
			$this->enabled = true;
			return;
		}
	}

	public function get_mode()
	{
		return $this->get_option('mode');
	}

	public function set_keymap(string $keymap)
	{
		$this->set_option('keyMap', $keymap);
	}

	public function get_keymap():string
	{
		return $this->get_option('keyMap') ?? 'default';
	}

	public function load_keymap(string $keymap)
	{
		$this->cm_js[dep::KEYMAPS[$keymap]] = true;
		$this->keymap_keys[$keymap] = true;
	}

	public function load_mode(string $mode)
	{
		$this->mode_keys[$mode] = true;
		$this->cm_js[dep::MODES[$mode]] = true;
	}

	public function load_all_themes()
	{
		foreach(dep::THEMES as $theme => $loc)
		{
			$this->load_theme($theme);
		}
	}

	public function load_theme(string $theme)
	{
		$this->theme_keys[$theme] = true;
		$this->cm_css[dep::THEMES[$theme]] = true;
	}

	public function load_addon(string $addon)
	{
		$this->addon_keys[$addon] = true;
		$this->cm_js[dep::ADDONS[$addon]] = true;
	}

	public function load_ext(string $ext)
	{
		$this->ext_js[$ext] = true;
	}

	public function load_custom_js(string $custom_js)
	{
		$this->custom_js[$custom_js] = true;
	}

	public function load_custom_css(string $custom_css)
	{
		$this->custom_css[$custom_css] = true;
	}

	public function set_history_id(string $history_id)
	{
		$this->history_id = $history_id;
	}

	public function set_default_content(string $default_content)
	{
		$this->default_content = $default_content;
	}
}
