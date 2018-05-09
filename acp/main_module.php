<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\acp;

use marttiphpbb\codemirror\util\cnst;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $phpbb_container;

		$request = $phpbb_container->get('request');
		$template = $phpbb_container->get('template');
		$language = $phpbb_container->get('language');
		$ext_manager = $phpbb_container->get('ext.manager');
		$store = $phpbb_container->get('marttiphpbb.codemirror.service.store');
		$listener = $phpbb_container->get('marttiphpbb.codemirror.listener');

		$phpbb_root_path = $phpbb_container->getParameter('core.root_path');
		$ext_relative_path = 'ext/' . cnst::FOLDER . '/';
		$ext_root_path = $phpbb_root_path . $ext_relative_path;

		$language->add_lang('acp', cnst::FOLDER);
		add_form_key(cnst::FOLDER);

		switch($mode)
		{
			case 'settings':

				$finder = $ext_manager->get_finder();

				$codemirror_dir = 'codemirror/';
				$theme_dir = $codemirror_dir . 'theme/';

				$files = $finder
					->extension_suffix('.css')
					->extension_directory($theme_dir)
					->find_from_extension(cnst::FOLDER, $ext_root_path);
				$themes = ['default'];
				
				foreach($files as $file => $ext)
				{
					$themes[] = str_replace([$ext_relative_path . $theme_dir, '.css'], '', $file);
				}

				sort($themes);

				foreach($themes as $theme)
				{
					$template->assign_block_vars('themes', [
						'NAME'	=> $theme,
					]);
				}

				$load_extra = [
					'themes'	=> $themes,
				];

				$listener->set('javascript', 'oufti', $load_extra);

				$this->tpl_name = 'settings';
				$this->page_title = $language->lang(cnst::L_ACP . '_SETTINGS');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					trigger_error($language->lang(cnst::L_ACP . '_SETTING_SAVED') . adm_back_link($this->u_action));
				}

				$data = $store->get_all();

				$template->assign_vars([
					'ACP_MARTTIPHPBB_CODEMIRROR_VERSION'	=> $data['version'],
					'ACP_MARTTIPHPBB_CODEMIRROR_THEME'		=> $data['theme'] ?? '',

				]);
	
				break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
