<?php

use Dreadnip\Flo\Flo;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class ProfileTest extends TestCase
{
    /**
     * Asserts that we can execute the profile method.
     */
    public function testProfile(): void
    {
        $flo = new Flo();
        $profile = $flo->getProfile('Zezima');
        $this->assertIsArray($profile);
    }
}
