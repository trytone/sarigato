<?php
namespace App\Entity;

class User
{
    private $id;

    private $email;

    private $password;

    public function getId(): int
    {
      return $this->id;
    }

    public function setId(int $id): void
    {
      $this->id = $id;
    }

    public function getEmail(): string
    {
      return $this->email;
    }

    public function setEmail(string $email): void
    {
      $this->email = $email;
    }

    public function getPassword(): string
    {
      return $this->password;
    }

    public function setPassword(string $password): void
    {
      $this->password = $password;
    }

}
