<?php

use Dreadnip\Flo\Flo;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class ClanListTest extends TestCase
{
    /**
     * Asserts that we can execute the clan list method for both existing and non-existing clans.
     */
    public function testClanList()
    {
        $flo = new Flo();
        $list = $flo->getClanList('fjiewrjvcrhncvhnsklcjwokhcwoehc');
        $this->assertNull($list);

        $list = $flo->getClanList('Maxed');
        $this->assertIsArray($list);
    }
}
