<?php

/**
 *
 * This file is part of the Macdom
 *
 * Copyright (c) 2015-2017 Vladimír Macháček
 *
 * For the full copyright and license information, please view the file license.md
 * that was distributed with this source code.
 *
 */

declare(strict_types = 1);

namespace Macdom\Bridges\Nette;

use Nette\DI\CompilerExtension;


class MacdomExtension extends CompilerExtension
{

	/**
	 * @var array
	 */
	protected $config = [
		'debugger' => '%debugMode%'
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->config);

		$builder->addDefinition($this->prefix('loader'))
			->setClass('Macdom\Bridges\Latte\FileLoader');

		$builder->addDefinition($this->prefix('engine'))
			->setClass('Macdom\Engine');

		if ($config['debugger']) {
			$builder->addDefinition($this->prefix('tracyPanel'))
				->setClass('Macdom\Bridges\MacdomTracy\MacdomPanel')
				->addSetup('setMacdom', ['@' . $this->prefix('engine')]);
		}
	}

}
