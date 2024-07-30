<?php

namespace App\Tests\Entity;

use App\Entity\Habitat;
use PHPUnit\Framework\TestCase;

class HabitatTest extends TestCase
{
    public function testGetAndSetNom(): void
    {
        $habitat = new Habitat();
        $nom = 'Test Nom';
        $habitat->setNom($nom);
        $this->assertEquals($nom, $habitat->getNom());
    }

    public function testGetAndSetDescription(): void
    {
        $habitat = new Habitat();
        $description = 'Test Description';
        $habitat->setDescription($description);
        $this->assertEquals($description, $habitat->getDescription());
    }

    public function testGetAndSetCommentaireHabitat(): void
    {
        $habitat = new Habitat();
        $commentaire = 'Test Commentaire';
        $habitat->setCommentaireHabitat($commentaire);
        $this->assertEquals($commentaire, $habitat->getCommentaireHabitat());
    }
}
