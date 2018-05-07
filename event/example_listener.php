<?php
/**
* phpBB Extension - marttiphpbb CodeMirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\event;

use phpbb\event\data as event;
use marttiphpbb\codemirror\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class example_listener implements EventSubscriberInterface
{
	/** @var bool */
	private $enable;

	/** @var string */
	private $mode;

	/**
	*/
	public function __construct()
	{
	}

	static public function getSubscribedEvents()
	{
		return [
			'marttiphpbb.codemirror.load'
				=> 'marttiphpbb_codemirror_load',
		];
	}

	public function load_codemirror(array $data)
	{
		$this->enable = true;
		$this->data = $data;	
	}

	public function marttiphpbb_codemirror_load(event $event)
	{
		if (!$this->enable)
		{
			return;
		}

		$enable = true;
		$mode = $this->data['mode'];
		$theme = $this->data['theme'];
		
		
		$event['enable'] = $enable;
		$event['mode'] = $mode;
		$event['theme'] = $theme;
	}
}
