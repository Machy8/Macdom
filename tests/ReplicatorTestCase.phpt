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


final class ReplicatorTestCase extends AbstractTestCase
{

	public function testElementsWithPlaceholders(): void
	{
		$this->assertMatchFile('elementsWithPlaceholders');
	}


	public function testReplicantWithKey(): void
	{
		$this->assertMatchFile('replicantWithKey');
	}


	public function testSimpleText(): void
	{
		$this->assertMatchFile('simpleText');
	}


	/**
	 * @throws\Macdom\CompileException No replicated line can be deregistered on line 2 near "	/@"
	 */
	public function testUnnecessaryReplicatorDeregistration(): void
	{
		$this->assertMatchFile('unnecessaryReplicatorDeregistration');
	}

}

run(new ReplicatorTestCase());
