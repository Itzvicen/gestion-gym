<?php

class Member
{
  private $id;
  private $first_name;
  private $last_name;
  private $email;
  private $phone;
  private $birth_date;
  private $registration_date;
  private $active;
  private $image_path;

  public function __construct($id, $first_name, $last_name, $email, $phone, $birth_date, $registration_date, $active, $image_path)
  {
    $this->id = $id;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->email = $email;
    $this->phone = $phone;
    $this->birth_date = $birth_date;
    $this->registration_date = $registration_date;
    $this->active = $active;
    $this->image_path = $image_path;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getFirstName()
  {
    return $this->first_name;
  }

  public function getLastName()
  {
    return $this->last_name;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function getPhone()
  {
    return $this->phone;
  }

  public function getBirthDate()
  {
    return $this->birth_date;
  }

  public function getRegistrationDate()
  {
    return $this->registration_date;
  }

  public function getActive()
  {
    return $this->active;
  }

  public function getImagePath()
  {
    return $this->image_path;
  }

  // Método estático para obtener todos los miembros
  public static function getAllMembers($db) {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM members');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Método estático para obtener todos los miembros por ordenacion
  public static function getOrderedMembers($order, $db) {
    $db = $db->getConnection();
    switch ($order) {
        case 'recent':
            $stmt = $db->prepare('SELECT * FROM members ORDER BY registration_date DESC');
            break;
        case 'old':
            $stmt = $db->prepare('SELECT * FROM members ORDER BY registration_date ASC');
            break;
        case 'alphabetical':
            $stmt = $db->prepare('SELECT * FROM members ORDER BY first_name ASC, last_name ASC');
            break;
        default:
            $stmt = $db->prepare('SELECT * FROM members');
            break;
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Método estático para crear un nuevo miembro
  public static function createMember($data, $db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("INSERT INTO members (first_name, last_name, email, phone, birth_date, registration_date, active, image_path) VALUES (:first_name, :last_name, :email, :phone, :birth_date, :registration_date, :active, :image_path)");
    $stmt->execute([
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'birth_date' => $data['birth_date'],
      'registration_date' => $data['registration_date'],
      'active' => $data['active'],
      'image_path' => $data['image_path']
    ]);

    return $db->lastInsertId(); // Retorna el ID del miembro creado
  }

  // Metodo  estatico para actualizar un miembro
  public static function updateMember($id, $data, $db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("UPDATE members SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, birth_date = :birth_date, registration_date = :registration_date, active = :active, image_path = :image_path WHERE id = :id");
    $stmt->execute([
      'first_name' => $data['first_name'],
      'last_name' => $data['last_name'],
      'email' => $data['email'],
      'phone' => $data['phone'],
      'birth_date' => $data['birth_date'],
      'registration_date' => $data['registration_date'],
      'active' => $data['active'],
      'image_path' => $data['image_path'],
      'id' => $id
    ]);

    return $stmt->rowCount(); // Retorna el número de filas afectadas
  }

  // Metodo estatico para eliminar un miembro
  public static function deleteMember($id, $db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("DELETE FROM members WHERE id = :id");
    $stmt->execute([
      'id' => $id
    ]);

    return $stmt->rowCount(); // Retorna el número de filas afectadas
  }

  // Método estático para buscar miembros por nombre
  public static function searchByName($query, $db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT * FROM members WHERE first_name LIKE :query OR last_name LIKE :query");
    $stmt->bindValue(':query', '%' . $query . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Metodo estatico para obtener miembros activos
  public static function getActiveMembers($db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM members WHERE active = 1");
    $stmt->execute();
    $active_members = $stmt->fetchColumn();

    return $active_members;
  }

  // Metodo estatico para obtener miembros inactivos
  public static function getInactiveMembers($db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM members WHERE active = 0");
    $stmt->execute();
    $inactive_members = $stmt->fetchColumn();

    return $inactive_members;
  }
}
