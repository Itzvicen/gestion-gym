<?php

/**
 * Clase Member
 *
 * Contiene métodos estáticos para realizar operaciones relacionadas con los miembros, como obtener miembros ordenados, crear, actualizar y eliminar miembros, 
 * buscar miembros por nombre, y obtener el número de miembros activos e inactivos.
 *
 */
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

  /**
   * Obtiene todos los miembros de la base de datos.
   *
   * @param $db La conexión a la base de datos.
   * @return array Los miembros obtenidos de la base de datos.
   */
  public static function getAllMembers($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT * FROM members");
    $stmt->execute();

    $members = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $members[] = new Member($row['id'], $row['first_name'], $row['last_name'], $row['email'], $row['phone'], $row['birth_date'], $row['registration_date'], $row['active'], $row['image_path']);
    }

    return $members;
  }

  /**
   * Obtiene un miembro por su id.
   *
   * @param int $memberId El id del miembro.
   * @param $db La conexión a la base de datos.
   * @return array El miembro obtenido de la base de datos.
   */
  public static function getMemberById($memberId, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT * FROM members WHERE id = :id");
    $stmt->execute([
      'id' => $memberId
    ]);

    $member = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $member = new Member(
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['email'],
        $row['phone'],
        $row['birth_date'],
        $row['registration_date'],
        $row['active'],
        $row['image_path']
      );
    }

    return $member;
  }

  /**
   * Método estático para obtener todos los miembros por ordenación.
   *
   * @param string $order El tipo de ordenación deseado ('recent', 'old', 'alphabetical').
   * @param $db La conexión a la base de datos.
   * @return array Un array con los datos de los miembros ordenados según el criterio especificado.
   */
  public static function getOrderedMembers($order, $db)
  {
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

    $members = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $members[] = new Member(
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['email'],
        $row['phone'],
        $row['birth_date'],
        $row['registration_date'],
        $row['active'],
        $row['image_path']
      );
    }

    return $members;
  }

  /**
   * Método estático para crear un nuevo miembro.
   *
   * @param array $data Los datos del nuevo miembro.
   * @param $db La conexión a la base de datos.
   * @return int El ID del miembro creado.
   */
  public static function createMember($data, $db)
  {
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

  /**
   * Método estático para actualizar un miembro existente.
   *
   * @param int $id El ID del miembro a actualizar.
   * @param array $data Los nuevos datos del miembro.
   * @param $db La conexión a la base de datos.
   * @return int El número de filas afectadas por la actualización.
   */
  public static function updateMember($id, $data, $db)
  {
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

  /**
   * Método estático para eliminar un miembro.
   *
   * @param int $id El ID del miembro a eliminar.
   * @param $db La conexión a la base de datos.
   * @return int El número de filas afectadas por la eliminación.
   */
  public static function deleteMember($id, $db)
  {
    $db = $db->getConnection();

    // Eliminar todos los pagos asociados al miembro
    $stmt = $db->prepare("DELETE FROM payments WHERE member_id = :id");
    $stmt->execute([
      'id' => $id
    ]);

    // Eliminar el miembro
    $stmt = $db->prepare("DELETE FROM members WHERE id = :id");
    $stmt->execute([
      'id' => $id
    ]);

    return $stmt->rowCount(); // Retorna el número de filas afectadas
  }

  /**
   * Método estático para buscar miembros por nombre.
   *
   * @param string $query El nombre o parte del nombre a buscar.
   * @param $db La conexión a la base de datos.
   * @return array Un array con los datos de los miembros que coinciden con la búsqueda.
   */
  public static function searchByName($query, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT * FROM members WHERE first_name LIKE :query OR last_name LIKE :query");
    $stmt->bindValue(':query', '%' . $query . '%');
    $stmt->execute();

    $members = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $members[] = new Member(
        $row['id'],
        $row['first_name'],
        $row['last_name'],
        $row['email'],
        $row['phone'],
        $row['birth_date'],
        $row['registration_date'],
        $row['active'],
        $row['image_path']
      );
    }

    return $members;
  }

  /**
   * Método estático para obtener el número de miembros activos.
   *
   * @param $db La conexión a la base de datos.
   * @return int El número de miembros activos.
   */
  public static function getActiveMembers($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM members WHERE active = 1");
    $stmt->execute();
    $active_members = $stmt->fetchColumn();

    return $active_members;
  }

  /**
   * Método estático para obtener el número de miembros inactivos.
   *
   * @param $db La conexión a la base de datos.
   * @return int El número de miembros inactivos.
   */
  public static function getInactiveMembers($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM members WHERE active = 0");
    $stmt->execute();
    $inactive_members = $stmt->fetchColumn();

    return $inactive_members;
  }
}
