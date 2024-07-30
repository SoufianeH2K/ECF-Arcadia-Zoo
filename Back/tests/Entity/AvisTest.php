<?php

namespace App\Tests\Entity;

use App\Entity\Avis;
use PHPUnit\Framework\TestCase;

class AvisTest extends TestCase
{
    public function testGetAndSetPseudo(): void
    {
        $avis = new Avis();
        $pseudo = 'TestPseudo';
        $avis->setPseudo($pseudo);
        $this->assertEquals($pseudo, $avis->getPseudo());
    }

    public function testGetAndSetCommentaire(): void
    {
        $avis = new Avis();
        $commentaire = 'Test Commentaire';
        $avis->setCommentaire($commentaire);
        $this->assertEquals($commentaire, $avis->getCommentaire());
    }

    public function testGetAndSetIsVisible(): void
    {
        $avis = new Avis();
        $isVisible = true;
        $avis->setIsVisible($isVisible);
        $this->assertEquals($isVisible, $avis->isIsVisible());
    }
}
