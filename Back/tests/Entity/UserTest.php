<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUsername(): void
    {
        $user = new User();
        $user->setUsername('test_user');
        $this->assertEquals('test_user', $user->getUsername());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setUsername('test_user');
        $this->assertEquals('test_user', $user->getUserIdentifier());
    }

    public function testRoles(): void
    {
        $user = new User();
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user->setRoles($roles);
        $this->assertEquals($roles, $user->getRoles());
    }

    public function testPassword(): void
    {
        $user = new User();
        $password = 'password123';
        $user->setPassword($password);
        $this->assertEquals($password, $user->getPassword());
    }

    public function testSalt(): void
    {
        $user = new User();
        $this->assertNull($user->getSalt());
    }

    public function testNom(): void
    {
        $user = new User();
        $nom = 'Doe';
        $user->setNom($nom);
        $this->assertEquals($nom, $user->getNom());
    }

    public function testPrenom(): void
    {
        $user = new User();
        $prenom = 'John';
        $user->setPrenom($prenom);
        $this->assertEquals($prenom, $user->getPrenom());
    }

    public function testApiToken(): void
    {
        $user = new User();
        $apiToken = 'random_api_token';
        $user->setApiToken($apiToken);
        $this->assertEquals($apiToken, $user->getApiToken());
    }
}
