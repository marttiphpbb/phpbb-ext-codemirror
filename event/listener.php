<?php
/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\event;

use phpbb\event\data as event;
use marttiphpbb\codemirror\service\load;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	private $load;

	public function __construct(load $load)
	{
		$this->load = $load;
	}

	static public function getSubscribedEvents():array
	{
		return [
			'core.twig_environment_render_template_before'
				=> 'core_twig_environment_render_template_before',
		];
	}

	public function core_twig_environment_render_template_before(event $event):void
	{
		if (!$this->load->is_enabled())
		{
			return;
		}

		$context = $event['context'];
		$context['marttiphpbb_codemirror'] = $this->load->get_listener_data();
		$event['context'] = $context;
	}
}
