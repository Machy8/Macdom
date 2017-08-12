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

namespace Macdom\Bridges\MacdomNette;

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

		$compiler = $builder->addDefinition($this->prefix('engine'))
			->setClass('Macdom\Engine');

		if ($config['debugger']) {
			$builder->addDefinition($this->prefix('tracyPanel'))
				->setClass('Macdom\Bridges\MacdomTracy\MacdomPanel');

			$compiler->addSetup('@' . $this->prefix('tracyPanel') . '::setMacdom', ['@' . $this->prefix('engine')]);
		}
	}

}
