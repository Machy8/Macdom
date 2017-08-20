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

namespace Macdom\Bridges\MacdomTracy;

use Macdom;
use Macdom\Engine;
use Macdom\Token;
use Tracy\Debugger;
use Tracy\IBarPanel;


class MacdomPanel implements IBarPanel
{

	const TEMPLATES_DIR = __DIR__ . '/templates';

	/**
	 * @var Engine
	 */
	private $macdom;


	public function __construct()
	{
		Debugger::getBar()->addPanel($this);
	}


	/**
	 * @return string
	 */
	public function getPanel(): string
	{
		ob_start();

		$installed = [
			'elements' => $this->getElements(),
			'macros' => $this->getMacros(),
			'elementsBooleanAttributes' => $this->getMacdom()->getElementsBooleanAttributes(),
		];

		require self::TEMPLATES_DIR . '/panel.phtml';

		return ob_get_clean();
	}


	public function getTab(): string
	{
		ob_start();

		require self::TEMPLATES_DIR . '/tab.phtml';

		return ob_get_clean();
	}


	public function setMacdom(Engine $macdom): self
	{
		$this->macdom = $macdom;

		return $this;
	}


	private function getMacdom(): Engine
	{
		if ( ! $this->macdom) {
			$this->macdom = new Engine;
		}

		return $this->macdom;
	}


	private function getElements(): array
	{
		$elementsByContentType = $this->getMacdom()->getElements();
		$panelElements = [];

		foreach ($elementsByContentType as $contentType => $elements) {
			$panelElements[$contentType] = [];

			foreach ($elements as $element => $details) {
				$panelElements[$contentType][$element] = [
					'quickAttributes' => $details['quickAttributes'] ?? NULL
				];
			}
		}

		return $panelElements;
	}


	private function getMacros(): array
	{
		$macrosByContentType = $this->getMacdom()->getMacros();
		$panelMacros = [];
		$type = 'Normal';

		foreach ($macrosByContentType as $contentType => $macros) {
			$panelMacros[$contentType] = [];

			foreach ($macros as $macro => $macroObject) {
				if (isset($macroObject['flags']) && in_array(Token::REGULAR_EXPRESSION_MACRO, $macroObject['flags'])) {
					$type = 'Regular expression';
				}

				$output = $macroObject['callback']('{line}', '{keyword}');
				$panelMacros[$contentType][$macro] = [
					'type' => $type,
					'output' => $output
				];
			}
		}

		return $panelMacros;
	}

}
