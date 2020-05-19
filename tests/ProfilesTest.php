<?php

use Dreadnip\Flo\Flo;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class ProfilesTest extends TestCase
{
    /**
     * Asserts that we can execute the clan list method for both existing and non-existing clans.
     */
    public function testProfiles(): void
    {
        $flo = new Flo();

        $list = $flo->getClanList('Maxed', true);

        $profiles = $flo->getProfiles($list);

        $this->assertIsArray($profiles);
    }
}
