<?php

namespace App\Tests\Entity;

use App\Entity\Service;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
      public function testNom(): void
    {
        $service = new Service();
        $nom = 'Test Service';
        $service->setNom($nom);
        $this->assertEquals($nom, $service->getNom());
    }

    public function testDescription(): void
    {
        $service = new Service();
        $description = 'This is a test description.';
        $service->setDescription($description);
        $this->assertEquals($description, $service->getDescription());
    }
}
