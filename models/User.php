<?php

class User
{
  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getAllUsers()
  {
    $stmt = $this->db->prepare('SELECT * FROM users');
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public static function getUserById($id, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function updateUser($id, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id');
    $stmt->execute([
      'first_name' => $_POST['first_name'],
      'last_name' => $_POST['last_name'],
      'email' => $_POST['email'],
      'id' => $id
    ]);
  }

  public function checkCredentials($username, $password)
  {
    $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username AND password = :password');
    $stmt->execute(['username' => $username, 'password' => $password]);
    return $stmt->fetch();
  }
}
