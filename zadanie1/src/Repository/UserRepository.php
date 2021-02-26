<?php
namespace App\Repository;

use App\Entity\User;
use App\Entity\Database;

class UserRepository
{
    private $database;

    public function __construct(){
      $this->database = new \mysqli("localhost","root","","sarigato");
    }

    public function find(int $id): array
    {
      $query = $this->database->query("SELECT id, email, password FROM `users` WHERE id = ".intval($id)." LIMIT 1");
      if($query){
        $result = $query->fetch_assoc();
        if($result){
          return $result;
        }
      }
      return [];
    }

    public function add(User &$user): bool
    {
        return $this->database->query(
          'INSERT INTO `users` (email, password) VALUES (\''.$user->getEmail().'\',\''.$user->getPassword().'\')'
        );
    }

    public function update(User &$user): bool
    {
      return $this->database->query(
        'UPDATE `users` SET email = \''.$user->getEmail().'\', password = \''.$user->getPassword().'\' WHERE id = '.$user->getId());
    }

    public function remove(int $id): bool
    {
        return $this->database->query("DELETE FROM `users` WHERE id =".intval($id));
    }

}
