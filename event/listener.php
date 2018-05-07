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

	/** @var bool */
	private $enable;

	/** @var string */
	private $mode;

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
		$enable = false;
		$mode = '';
	
		/**
		 * @event 
		 * @var bool $enable
		 * @var string $mode javascript, css, html, php
		 */
		$vars = ['enable', 'mode'];
		extract($this->dispatcher->trigger_event('marttiphpbb.codemirror.load', compact($vars)));

	}

	public function core_twig_environment_render_template_before(event $event)
	{
		if (!$this->enable)
		{
			return;
		}

		$context = $event['context'];

		$context['marttiphpbb_codemirror'] = [

		];

		$event['context'] = $context;
	}
}
