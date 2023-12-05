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

  public static function updateUser($userId, $firstName, $lastName, $email, $db)
  {
    $db = $db->getConnection();
    // Preparar la consulta SQL
    $stmt = $db->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id");

    // Vincular los parámetros
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $userId);

    // Ejecutar la consulta
    $stmt->execute();
    return $stmt->rowCount();
  }

  public function checkCredentials($username, $password)
  {
    $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      return $user;
    }

    return null;
  }

  public static function updatePassword($userId, $newPassword, $db)
  {
    // Encriptar la nueva contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Preparar la consulta SQL
    $db = $db->getConnection();
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");

    // Vincular los parámetros
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':id', $userId);

    // Ejecutar la consulta
    $stmt->execute();
    return $stmt->rowCount();
  }
}
