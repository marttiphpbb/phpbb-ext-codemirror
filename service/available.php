<?php

/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\service;

use phpbb\extension\manager as ext_manager;
use marttiphpbb\codemirror\util\cnst;

class available
{
	/** @var ext_manager */
	private $ext_manager;

	/** @var string */
	private $phpbb_root_path;

	/** @var string */
	private $ext_root_path;

	/**
	 * @param ext_manager
	 * @param string
	 */
	public function __construct(
		ext_manager $ext_manager,
		string $phpbb_root_path
	)
	{
		$this->ext_manager = $ext_manager;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->init();	
	}

	private function init()
	{
		$this->finder = $this->ext_manager->get_finder();
		$this->ext_root_path = $this->phpbb_root_path . cnst::EXT_PATH;		
	}

	public function get_themes():array 
	{
		$files = $this->finder
			->extension_suffix('.css')
			->extension_directory(cnst::THEME_DIR)
			->find_from_extension(cnst::FOLDER, $this->ext_root_path);

		$themes = [];
				
		foreach($files as $file => $ext)
		{
			$themes[] = str_replace([cnst::EXT_PATH . cnst::THEME_DIR, '.css'], '', $file);
		}

		sort($themes);

		return array_merge(['default'], $themes);	
	}

	public function get_modes():array
	{
		$files = $this->finder
			->extension_suffix('.js')
			->extension_directory(cnst::MODE_DIR)
			->find_from_extension(cnst::FOLDER, $this->ext_root_path);

		$modes = [];
				
		foreach($files as $file => $ext)
		{
			$modes[] = str_replace([cnst::EXT_PATH . cnst::MODE_DIR, '.js'], '', $file);
		}

		sort($modes);

		return $modes;
	}

	public function set(string $key, string $value)
	{

	}

}
