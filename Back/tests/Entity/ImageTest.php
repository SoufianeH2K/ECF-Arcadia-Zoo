<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Entity\Habitat;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testGetAndSetImageData(): void
    {
        $image = new Image();
        $imageData = fopen('php://memory', 'rb+');
        fwrite($imageData, 'image data');
        rewind($imageData);

        $image->setImageData($imageData);
        $this->assertSame($imageData, $image->getImageData());
    }

    public function testGetHabitat(): void
    {
        $image = new Image();
        $this->assertInstanceOf(Collection::class, $image->getHabitat());
    }

    public function testAddHabitat(): void
    {
        $image = new Image();
        $habitat = new Habitat();

        $image->addHabitat($habitat);
        $this->assertTrue($image->getHabitat()->contains($habitat));
    }

    public function testRemoveHabitat(): void
    {
        $image = new Image();
        $habitat = new Habitat();

        $image->addHabitat($habitat);
        $this->assertTrue($image->getHabitat()->contains($habitat));

        $image->removeHabitat($habitat);
        $this->assertFalse($image->getHabitat()->contains($habitat));
    }
}
