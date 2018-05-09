<?php
/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\event;

use phpbb\event\data as event;
use phpbb\event\dispatcher;
use marttiphpbb\codemirror\service\store;
use marttiphpbb\codemirror\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var dispatcher */
	private $dispatcher;

	/** @var store */
	private $store;

	/** @var string */
	private $phpbb_root_path;

	/** @var string */
	private $mode = '';

	/** @var string */
	private $history_id;

	/** @var array */
	private $load_extra = [];

	/** @var array */
	private $overwrite_config = [];

	/**
	 * @param store
	 * @param string
	*/
	public function __construct(
		dispatcher $dispatcher, 
		store $store, 
		string $phpbb_root_path
	)
	{
		$this->dispatcher = $dispatcher;
		$this->store = $store;
		$this->phpbb_root_path = $phpbb_root_path;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.twig_environment_render_template_before'
				=> 'core_twig_environment_render_template_before',
			'core.adm_page_header_after'
				=> 'core_adm_page_header_after',
		];
	}

	public function core_adm_page_header_after(event $event)
	{
		$mode = '';
		$history_id = '';
		$load_extra = [];
		$overwrite_config = [];
	
		/**
		 * @event 
		 * @var string $mode javascript, css, html, php, twig, yaml, json, markdown, ...
		 * @var history_id 
		 * @var array $load_extra
		 * @var array $overwrite_config
		 */
		$vars = ['mode', 'load_extra', 'override_config'];
		extract($this->dispatcher->trigger_event('marttiphpbb.codemirror.load', compact($vars)));

		if ($mode)
		{
			$this->mode = $mode;
			$this->history_id = $history_id;
			$this->load_extra = $load_extra;
			$this->override_config = $override_config;
		}
	}

	/**
	 * @param string $mode javascript, css, html, php, twig, yaml 
	 * @param array $load_extra
	 * @param array $override_config 
	 */
	public function set(string $mode, string $history_id = '', array $load_extra = [], array $override_config = [])
	{
		$this->mode = $mode;
		$this->history_id = $history_id;
	}

	public function core_twig_environment_render_template_before(event $event)
	{
		if (!$this->mode)
		{
			return;
		}

		$context = $event['context'];
		$data = $this->store->get_all();

		$config = [
			'lineNumbers' 	=> true,
			'theme'			=> $data['theme'] ?? '3024-day',
			'mode'			=> $this->mode ?? $data['mode'] ?? 'htmlembedded',
		];

		$data_attr = ' data-marttiphpbb-codemirror="%s"';
		$config = htmlspecialchars(json_encode($config), ENT_QUOTES, 'UTF-8');
		$data_attr = sprintf($data_attr, $config);

		if ($this->history_id !== '')
		{
			$data_attr .= ' data-marttiphpbb-codemirror-history-id="';
			$data_attr .= $this->history_id . '"';
		}

		$context['marttiphpbb_codemirror'] = [
			'data'			=> $data_attr,
			'mode'			=> $this->mode,
			'load'			=> [
				'themes'	=> $themes,
			],
			'theme' 		=> $data['theme'] ?? '3024-day',
	
			'version_param'	=> '?v=' . $data['version'],
			'version'		=> $data['version'],
			'path'			=> $this->phpbb_root_path . 'ext/' . cnst::FOLDER . '/codemirror/',
		];

		$event['context'] = $context;
	}
}
