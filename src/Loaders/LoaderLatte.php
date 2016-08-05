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

use Latte\Loaders\FileLoader;


class LoaderLatte extends FileLoader implements ILoader
{

	use LoadersCore;


	/**
	 * @param string $file
	 * @return string
	 */
	public function getContent($file)
	{
		$content = parent::getContent($file);
		$compiled = $this->compile($content);

		return $compiled;
	}

}
