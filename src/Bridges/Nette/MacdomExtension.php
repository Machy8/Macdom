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

	const
		ENGINE_CLASSNAME = 'Macdom\Engine',

		TRACY_CLASSNAME = 'Tracy\Debugger',
		TRACY_PANEL_CLASSNAME = 'Macdom\Bridges\Tracy\MacdomPanel';

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
			->setClass(self::ENGINE_CLASSNAME);

		if ($config['debugger']) {
			$builder->addDefinition($this->prefix('tracyPanel'))
				->setClass(self::TRACY_PANEL_CLASSNAME)
				->addSetup('setMacdom', ['@' . $this->prefix('engine')]);
		}
	}


	public function afterCompile(\Nette\PhpGenerator\ClassType $classType)
	{
		if ($this->config['debugger'] !== TRUE || ! class_exists(self::TRACY_CLASSNAME)) {
			return;
		}

		$classType->getMethod('initialize')->addBody(
			'$this->getByType("' . self::TRACY_PANEL_CLASSNAME
			. '")->setMacdom($this->getByType("' . self::ENGINE_CLASSNAME . '"));'
		);
	}

}
