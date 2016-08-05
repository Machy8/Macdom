<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2016 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md that was distributed with this source code.
 *
 */

namespace Machy8\Macdom\Loaders;


interface ILoader
{
	
	/**
	 * ILoader constructor.
	 */
	public function __construct ();


	/**
	 * @param string $content
	 * @return string
	 */
	public function compile ($content);
	
}
