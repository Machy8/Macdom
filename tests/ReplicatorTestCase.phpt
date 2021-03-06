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

namespace Macdom\Tests;

require_once 'bootstrap.php';

/**
 * @testCase
 */
final class ReplicatorTestCase extends AbstractTestCase
{

	public function testElementsWithPlaceholders()
	{
		$this->assertMatchFile('elementsWithPlaceholders');
	}


	public function testReplicaWithKey()
	{
		$this->assertMatchFile('replicaWithKey');
	}


	public function testSimpleText()
	{
		$this->assertMatchFile('simpleText');
	}


	/**
	 * @throws\Macdom\CompileException No replicated line can be deregistered on line 2 near "	/@"
	 */
	public function testUnnecessaryReplicatorDeregistration()
	{
		$this->assertMatchFile('unnecessaryReplicatorDeregistration');
	}

}

(new ReplicatorTestCase())->run();
