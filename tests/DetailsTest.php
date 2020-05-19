<?php

use Dreadnip\Flo\Flo;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class DetailsTest extends TestCase
{
    /**
     * Asserts that we can execute the clan list method for both existing and non-existing clans.
     */
    public function testDetails(): void
    {
        $flo = new Flo();

        $list = $flo->getClanList('Maxed', true);

        $profiles = $flo->getDetails($list);

        $this->assertIsArray($profiles);
    }
}
