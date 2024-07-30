<?php

namespace App\Tests\Entity;

use App\Entity\Race;
use PHPUnit\Framework\TestCase;

class RaceTest extends TestCase
{
    public function testLabel(): void
    {
        $race = new Race();
        $label = 'Test Label';
        $race->setLabel($label);
        $this->assertEquals($label, $race->getLabel());
    }
}
