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

		$load = $phpbb_container->get('marttiphpbb.codemirror.load');
		$store = $phpbb_container->get('marttiphpbb.codemirror.store');

		$language->add_lang('acp', cnst::FOLDER);
		add_form_key(cnst::FOLDER);

		switch($mode)
		{
			case 'config':

				$this->tpl_name = 'config';
				$this->page_title = $language->lang(cnst::L_ACP . '_CONFIG');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$config = trim($request->variable('config', ''));

					$json = json_decode($config, true);

					if (!isset($json))
					{
						trigger_error($language->lang(cnst::L_ACP . '_INVALID_JSON') . adm_back_link($this->u_action), E_USER_WARNING);
					}

					$store->set('config', $config);

					trigger_error($language->lang(cnst::L_ACP . '_CONFIG_SAVED') . adm_back_link($this->u_action));
				}

				$load->set_mode('json');
				$load->load_all_themes();

				$config = $store->get('config');
		
				if (!$config)
				{
					$config = file_get_contents(__DIR__ . '/../default_config.json');
				}

				$template->assign_var('CONFIG', $config);
	
				break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
