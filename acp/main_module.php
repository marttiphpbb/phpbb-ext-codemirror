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

					$config = $request->variable('config', '', true);
					$config = htmlspecialchars_decode($config);
					$json = json_decode($config, true);

					if (!isset($json))
					{
						switch (json_last_error()) 
						{
							case JSON_ERROR_DEPTH:
								$err = cnst::L_ACP . '_JSON_ERROR_DEPTH';
							break;
							case JSON_ERROR_STATE_MISMATCH:
								$err = cnst::L_ACP . '_JSON_ERROR_STATE_MISMATCH';
							break;
							case JSON_ERROR_CTRL_CHAR:
								$err = cnst::L_ACP . '_JSON_ERROR_CTRL_CHAR';
							break;
							case JSON_ERROR_SYNTAX:
								$err = cnst::L_ACP . '_JSON_ERROR_SYNTAX';
							break;
							case JSON_ERROR_UTF8:
								$err = cnst::L_ACP . '_JSON_ERROR_UTF8';
							break;
							default:
								$err = cnst::L_ACP . '_JSON_ERROR_UNKNOWN';
							break;
						}

						trigger_error($language->lang($err) . adm_back_link($this->u_action), E_USER_WARNING);
					}

					$store->set('config', $config);

					trigger_error($language->lang(cnst::L_ACP . '_CONFIG_SAVED') . adm_back_link($this->u_action));
				}

				$default_content = file_get_contents(__DIR__ . '/../default_config.json');

				$load->set_mode('json');
				$load->load_all_themes();
				$load->set_history_id('marttiphpbb_codemirror_configuration');
				$load->set_default_content($default_content);

				$config = $store->get('config');
	
				$template->assign_var('CONFIG', $config ?? $default_content);
	
			break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
