<?php

namespace App\Tests\Entity;

use App\Entity\RapportVeterinaire;
use App\Entity\User;
use App\Entity\Animal;
use PHPUnit\Framework\TestCase;

class RapportVeterinaireTest extends TestCase
{
    public function testDate(): void
    {
        $rapport = new RapportVeterinaire();
        $date = new \DateTime('2023-05-16');
        $rapport->setDate($date);
        $this->assertEquals($date, $rapport->getDate());
    }

    public function testDetail(): void
    {
        $rapport = new RapportVeterinaire();
        $detail = 'Test detail';
        $rapport->setDetail($detail);
        $this->assertEquals($detail, $rapport->getDetail());
    }

    public function testUtilisateur(): void
    {
        $rapport = new RapportVeterinaire();
        $user = new User();
        $rapport->setUtilisateur($user);
        $this->assertSame($user, $rapport->getUtilisateur());
    }

    public function testAnimal(): void
    {
        $rapport = new RapportVeterinaire();
        $animal = new Animal();
        $rapport->setAnimal($animal);
        $this->assertSame($animal, $rapport->getAnimal());
    }
}
