<?php

namespace App\Tests\Entity;

use App\Entity\Animal;
use App\Entity\Race;
use PHPUnit\Framework\TestCase;

class AnimalTest extends TestCase
{
    public function testGetAndSetPrenom(): void
    {
        $animal = new Animal();
        $prenom = 'Buddy';
        $animal->setPrenom($prenom);
        $this->assertEquals($prenom, $animal->getPrenom());
    }

    public function testGetAndSetEtat(): void
    {
        $animal = new Animal();
        $etat = 'Healthy';
        $animal->setEtat($etat);
        $this->assertEquals($etat, $animal->getEtat());
    }

    public function testGetAndSetRace(): void
    {
        $animal = new Animal();
        $race = new Race();
        $animal->setRace($race);
        $this->assertSame($race, $animal->getRace());
    }
}
