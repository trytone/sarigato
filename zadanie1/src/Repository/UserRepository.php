<?php
namespace App\Repository;

class UserRepository
{
    private $database;

    public function __construct(){
      $this->database = new \PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    }

    public function find(int $id)
    {
      $query = $this->database->prepare("SELECT id, email, password FROM `users` WHERE id = ? LIMIT 1");
      $query->execute([intval($id)]); // sql injection prevent by binding values and intval
      return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public function add(array $userArray): int
    {
      if(!filter_var($userArray['email'], FILTER_VALIDATE_EMAIL)) return 0;
      $query = $this->database->prepare("INSERT INTO `users` (email, password) VALUES (?,?)");
      $query->execute([$userArray['email'], hash('sha512', $userArray['password'] )]); // sql injection prevent by binding values
      return $this->database->lastInsertId();
    }

    public function update(array $userArray): bool
    {
      if(!filter_var($userArray['email'], FILTER_VALIDATE_EMAIL)) return false;
      $query = $this->database->prepare("UPDATE `users` SET email = ?, password = ? WHERE id = ?");
      return $query->execute([$userArray['email'], hash('sha512', $userArray['password'] ), intval($userArray['id'])]); // sql injection prevent
    }

    public function remove(int $id): bool
    {
        $query = $this->database->prepare("DELETE FROM `users` WHERE id = ?");
        return $query->execute([intval($id)]);
    }

}
