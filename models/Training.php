<?php

/**
 * Clase Training
 *
 * Esta clase representa un entrenamiento.
 *
 */
class Training
{
  private $db;
  private $id;
  private $class_name;
  private $class_days;
  private $class_time;
  private $class_duration;
  private $instructor;
  private $location;
  private $poster_image;

  public function __construct($db, $id, $class_name, $class_days, $class_time, $class_duration, $instructor, $location, $poster_image)
  {
    $this->db = $db;
    $this->id = $id;
    $this->class_name = $class_name;
    $this->class_days = $class_days;
    $this->class_time = $class_time;
    $this->class_duration = $class_duration;
    $this->instructor = $instructor;
    $this->location = $location;
    $this->poster_image = $poster_image;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getClassName()
  {
    return $this->class_name;
  }

  public function getClassDays()
  {
    return $this->class_days;
  }

  public function getClassTime()
  {
    return $this->class_time;
  }

  public function getClassDuration()
  {
    return $this->class_duration;
  }

  public function getInstructor()
  {
    return $this->instructor;
  }

  public function getLocation()
  {
    return $this->location;
  }

  public function getPosterImage()
  {
    return $this->poster_image;
  }

  // Setters

  public function setClassName($class_name)
  {
    $this->class_name = $class_name;
  }

  public function setClassDays($class_days)
  {
    $this->class_days = $class_days;
  }

  public function setClassTime($class_time)
  {
    $this->class_time = $class_time;
  }

  public function setClassDuration($class_duration)
  {
    $this->class_duration = $class_duration;
  }

  public function setInstructor($instructor)
  {
    $this->instructor = $instructor;
  }

  public function setLocation($location)
  {
    $this->location = $location;
  }

  public function setPosterImage($poster_image)
  {
    $this->poster_image = $poster_image;
  }


  /**
   * Obtener todos los entrenamientos
   *
   * Este método obtiene todos los entrenamientos registrados en la base de datos.
   *
   * @param object $db La conexión a la base de datos.
   * @return array Los entrenamientos encontrados.
   */
  public static function getAllTrainings($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM training_classes');
    $stmt->execute();

    $trainings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $training = new Training(
        $db,
        $row['id'],
        $row['class_name'],
        $row['class_days'],
        $row['class_time'],
        $row['class_duration'],
        $row['instructor'],
        $row['location'],
        $row['poster_image']
      );

      array_push($trainings, $training);
    }

    return $trainings;
  }

  /**
   * Obtener un entrenamiento por su ID
   *
   * @param int $id El ID del entrenamiento
   * @param object $db La conexión a la base de datos
   * @return array El entrenamiento encontrado
   *
   */
  public static function getTrainingById($id, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM training_classes WHERE id = :id');
    $stmt->execute(['id' => $id]);
    
    $training = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $training = new Training(
        $db,
        $row['id'],
        $row['class_name'],
        $row['class_days'],
        $row['class_time'],
        $row['class_duration'],
        $row['instructor'],
        $row['location'],
        $row['poster_image']
      );
    }

    return $training;
  }

  /**
   * Crear un nuevo entrenamiento
   *
   * @param string $class_name Nombre del entrenamiento
   * @param string $class_days Días del entrenamiento
   * @param string $class_time Hora del entrenamiento
   * @param string $class_duration Duración del entrenamiento
   * @param string $instructor Instructor del entrenamiento
   * @param string $location Ubicación del entrenamiento
   * @param string $poster_image Imagen del entrenamiento
   * @param object $db La conexión a la base de datos
   *
   * @return int El ID del entrenamiento creado
   */
  public static function createTraining($class_name, $class_days, $class_time, $class_duration, $instructor, $location, $poster_image, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('INSERT INTO training_classes (class_name, class_days, class_duration, class_time, instructor, location, poster_image) VALUES (:class_name, :class_days, :class_duration, :class_time, :instructor, :location, :poster_image)');
    $stmt->execute([
      'class_name' => $class_name,
      'class_days' => $class_days,
      'class_time' => $class_time,
      'class_duration' => $class_duration,
      'instructor' => $instructor,
      'location' => $location,
      'poster_image' => $poster_image
    ]);
    return $db->lastInsertId();
  }

  /**
   * Actualizar un entrenamiento
   *
   * @param int $id ID del entrenamiento a actualizar
   * @param string $class_name Nombre del entrenamiento
   * @param string $class_days Días del entrenamiento
   * @param string $class_time Hora del entrenamiento
   * @param string $class_duration Duración del entrenamiento
   * @param string $instructor Instructor del entrenamiento
   * @param string $location Ubicación del entrenamiento
   * @param string $poster_image Imagen del entrenamiento
   * @param object $db La conexión a la base de datos
   *
   * @return int El número de filas afectadas
   */
  public static function updateTraining($id, $class_name, $class_days, $class_duration, $class_time, $instructor, $location, $poster_image, $db) 
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('UPDATE training_classes SET class_name = :class_name, class_days = :class_days, class_duration = :class_duration, class_time = :class_time, instructor = :instructor, location = :location, poster_image = :poster_image WHERE id = :id');
    $stmt->execute([
      'class_name' => $class_name,
      'class_days' => $class_days,
      'class_duration' => $class_duration,
      'class_time' => $class_time,
      'instructor' => $instructor,
      'location' => $location,
      'poster_image' => $poster_image,
      'id' => $id
    ]);

    return $stmt->rowCount();
  }

  /**
   * Eliminar un entrenamiento
   *
   * @param int $id ID del entrenamiento a eliminar
   * @param object $db La conexión a la base de datos
   *
   * @return int El número de filas afectadas
   */
  public static function deleteTraining($id, $db)
  {
    $db = $db->getConnection();

    // Eliminar las filas de member_classes que hacen referencia a este entrenamiento
    $stmt = $db->prepare('DELETE FROM member_classes WHERE class_id = :id');
    $stmt->execute([
      'id' => $id
    ]);

    // Ahora puedes eliminar el entrenamiento
    $stmt = $db->prepare('DELETE FROM training_classes WHERE id = :id');
    $stmt->execute([
      'id' => $id
    ]);

    return $stmt->rowCount();
  }

  /**
   * Obtener los miembros de un entrenamiento por su ID
   *
   * @param int $trainingId ID del entrenamiento
   * @param object $db La conexión a la base de datos
   *
   * @return array Los miembros encontrados
   */
  public static function getMembersByTrainingId($trainingId, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('
        SELECT members.*
        FROM members
        INNER JOIN member_classes ON members.id = member_classes.member_id
        WHERE member_classes.class_id = :classId
    ');
    $stmt->execute([
      'classId' => $trainingId
    ]);

    $members = [];

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

      array_push($members, $member);
    }

    return $members;
  }

  /**
   * Obtener los entrenamientos de un miembro por su ID
   *
   * @param int $memberId ID del miembro
   * @param object $db La conexión a la base de datos
   *
   * @return array Los entrenamientos encontrados
   */
  public static function getTrainingsByMemberId($memberId, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('
      SELECT training_classes.*
      FROM training_classes
      INNER JOIN member_classes ON training_classes.id = member_classes.class_id
      WHERE member_classes.member_id = :memberId
    ');
    $stmt->execute([
      'memberId' => $memberId
    ]);
    
    $trainings = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $training = new Training(
        $db,
        $row['id'],
        $row['class_name'],
        $row['class_days'],
        $row['class_time'],
        $row['class_duration'],
        $row['instructor'],
        $row['location'],
        $row['poster_image']
      );

      array_push($trainings, $training);
    }

    return $trainings;
  }

  /**
   * Obtener los entrenamientos de un miembro por su ID
   *
   * @param int $memberId ID del miembro
   * @param object $db La conexión a la base de datos
   *
   * @return array Los entrenamientos encontrados
   */
  public static function addMemberToTraining($memberId, $trainingId, $db)
  {
    $db = $db->getConnection();

    // Comprobar si el miembro ya está inscrito en la clase
    $stmt = $db->prepare('SELECT * FROM member_classes WHERE member_id = :memberId AND class_id = :trainingId');
    $stmt->execute([
      'memberId' => $memberId,
      'trainingId' => $trainingId
    ]);
    $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingEntry) {
      // El miembro ya está inscrito en la clase
      return false;
    } else {
      // El miembro no está inscrito en la clase, así que se añade
      $stmt = $db->prepare('INSERT INTO member_classes (member_id, class_id) VALUES (:memberId, :trainingId)');
      $stmt->execute([
        'memberId' => $memberId,
        'trainingId' => $trainingId
      ]);

      // Obtener la información del miembro
      $stmt = $db->prepare('SELECT * FROM members WHERE id = :memberId');
      $stmt->execute([
        'memberId' => $memberId
      ]);

      $member = $stmt->fetch(PDO::FETCH_ASSOC);

      // Obtener la información de la clase
      $stmt = $db->prepare('SELECT * FROM training_classes WHERE id = :trainingId');
      $stmt->execute([
        'trainingId' => $trainingId
      ]);

      $training = $stmt->fetch(PDO::FETCH_ASSOC);

      // Enviar mensaje de WhatsApp al miembro
      // Crear una instancia de WhatsAppSender
      $whatsappSender = new WhatsAppSender('https://graph.facebook.com/v18.0/176913302172866/messages', $_ENV['WHATSAPP_TOKEN']);

      // Preparar los datos para el mensaje
      $prefijo = '34';
      $to = $prefijo . $member['phone']; // Asegúrate de que el número de teléfono esté en formato internacional completo
      $firstName = $member['first_name'];
      $lastName = $member['last_name'];
      $className = $training['class_name'];
      $classDays = $training['class_days'];

      // Enviar el mensaje
      $whatsappSender->sendAddedMessage($to, $firstName, $lastName, $className, $classDays);

      return true;
    }
  }

  /**
   * Eliminar un miembro de un entrenamiento
   *
   * @param int $memberId ID del miembro
   * @param int $trainingId ID del entrenamiento
   * @param object $db La conexión a la base de datos
   *
   * @return int El número de filas afectadas
   */
  public static function deleteMemberFromClass($memberId, $trainingId, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('DELETE FROM member_classes WHERE member_id = :memberId AND class_id = :trainingId');
    $stmt->execute([
      'memberId' => $memberId,
      'trainingId' => $trainingId
    ]);

    $rowsAffected = $stmt->rowCount();

    if ( $rowsAffected > 0 ) {
      // Obtener la información del miembro
      $stmt = $db->prepare('SELECT * FROM members WHERE id = :memberId');
      $stmt->execute([
        'memberId' => $memberId
      ]);
      $member = $stmt->fetch(PDO::FETCH_ASSOC);

      $prefijo = '34';
      $to = $prefijo . $member['phone'];
      $firstName = $member['first_name'];
      $lastName = $member['last_name'];

      // Enviar mensaje de WhatsApp al miembro
      $whatsappSender = new WhatsAppSender('https://graph.facebook.com/v18.0/176913302172866/messages', $_ENV['WHATSAPP_TOKEN']);
      $whatsappSender->sendRemovedMessage($to, $firstName, $lastName);
  
      return $rowsAffected;
    }

    return false;
  }
}
