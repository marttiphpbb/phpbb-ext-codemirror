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
//		$finder = $phpbb_container->get('ext.finder');
		$store = $phpbb_container->get('marttiphpbb.codemirror.service.store');
		$example_listener = $phpbb_container->get('marttiphpbb.codemirror.example_listener');
	
		$language->add_lang('acp', cnst::FOLDER);
		add_form_key(cnst::FOLDER);

		switch($mode)
		{
			case 'settings':

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



				$example_listener->load_codemirror($data);


				$template->assign_vars([
					'ACP_MARTTIPHPBB_CODEMIRROR_VERSION'	=> $data['version'],
					'ACP_MARTTIPHPBB_CODEMIRROR_THEME'		=> $data['theme'] ?? '',

				]);
	
				break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
