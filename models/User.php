<?php

/**
 * Clase User
 *
 * Contiene métodos para obtener todos los usuarios, obtener un usuario por su ID,
 * actualizar un usuario, verificar las credenciales de un usuario y actualizar la contraseña de un usuario.
 */
class User
{
  private $db;
  private $id;
  private $username;
  private $firstName;
  private $lastName;
  private $email;
  private $password;
  private $role;

  public function __construct($db, $id = null, $username = null, $firstName = null, $lastName = null, $email = null, $password = null, $role = null)
  {
    $this->db = $db;
    $this->id = $id;
    $this->username = $username;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->email = $email;
    $this->password = $password;
    $this->role = $role;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function getFirstName()
  {
    return $this->firstName;
  }

  public function getLastName()
  {
    return $this->lastName;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getRole()
  {
    return $this->role;
  }

  public function getPassword()
  {
    return $this->password;
  }

  public function setUsername($username)
  {
    $this->username = $username;
  }

  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }

  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }

  public function setEmail($email)
  {
    $this->email = $email;
  }

  public function setRole($role)
  {
    $this->role = $role;
  }

  public function setPassword($password)
  {
    $this->password = $password;
  }

  /**
   * Obtener las iniciales del nombre y apellido del usuario
   *
   * @return string Iniciales del nombre y apellido del usuario
   */
  public function getInitials()
  {
    return strtoupper(substr($this->firstName, 0, 1) . substr($this->lastName, 0, 1));
  }

  /**
   * Obtener todos los usuarios
   *
   * @return array Arreglo con todos los usuarios
   */
  public function getAllUsers()
  {
    $stmt = $this->db->prepare('SELECT * FROM users');
    $stmt->execute();
    return $stmt->fetchAll();
  }

  /**
   * Obtener un usuario por su ID
   *
   * @param int $id ID del usuario a obtener
   * @param object $db Objeto de conexión a la base de datos
   * @return object Objeto con los datos del usuario si existe, o null si no existe
   */
  public static function getUserById($id, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      return new User($db, $user['id'], $user['username'], $user['first_name'], $user['last_name'], $user['email'], $user['password'], $user['role']);
    } else {
      return null;
    }
  }
  
  /**
   * Actualizar un usuario
   *
   * @param int $userId ID del usuario a actualizar
   * @param string $firstName Nuevo nombre del usuario
   * @param string $lastName Nuevo apellido del usuario
   * @param string $email Nuevo correo electrónico del usuario
   * @param object $db Objeto de conexión a la base de datos
   * @return int Número de filas afectadas por la actualización
   */
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

  /**
   * Verificar las credenciales de un usuario
   *
   * @param string $username Nombre de usuario
   * @param string $password Contraseña
   * @return array|null Arreglo con los datos del usuario si las credenciales son válidas, o null si no lo son
   */
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

  /**
   * Actualizar la contraseña de un usuario
   *
   * @param int $userId ID del usuario a actualizar
   * @param string $newPassword Nueva contraseña del usuario
   * @param object $db Objeto de conexión a la base de datos
   * @return int Número de filas afectadas por la actualización
   */
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
