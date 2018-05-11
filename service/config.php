<?php

/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\service;

use marttiphpbb\codemirror\service\store;
use marttiphpbb\codemirror\util\cnst;

class config
{
	/** @var store */
	private $store;

	/**
	 * @param store
	 */
	public function __construct(
		store $store
	)
	{
		$this->store = $store;	
	}


}
