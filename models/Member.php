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
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $members = [];
    foreach ($results as $row) {
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