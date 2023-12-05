<?php

class Training
{
  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public static function getAllTrainings($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM training_classes');
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public static function getTrainingById($id, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM training_classes WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Crear un nuevo entrenamiento
  public function createTraining($class_name, $class_date, $class_duration, $instructor, $location, $poster_image)
  {
    $db = $this->db->getConnection();
    $stmt = $db->prepare('INSERT INTO training_classes (class_name, class_date, class_duration, instructor, location, poster_image) VALUES (:class_name, :class_date, :class_duration, :instructor, :location :poster_image)');
    $stmt->execute([
      'class_name' => $class_name,
      'class_date' => $class_date,
      'class_duration' => $class_duration,
      'instructor' => $instructor,
      'location' => $location,
      'poster_image' => $poster_image
    ]);
    return $db->lastInsertId();
  }

  // Actualizar un entrenamiento
  public function updateTraining($id, $class_name, $class_date, $class_duration, $instructor, $location, $poster_image)
  {
    $db = $this->db->getConnection();
    $stmt = $db->prepare('UPDATE training_classes SET class_name = :class_name, class_date = :class_date, class_duration = :class_duration, instructor = :instructor, location = :location, poster_image = :poster_image WHERE id = :id');
    $stmt->execute([
      'id' => $id,
      'class_name' => $class_name,
      'class_date' => $class_date,
      'class_duration' => $class_duration,
      'instructor' => $instructor,
      'location' => $location,
      'poster_image' => $poster_image
    ]);

    return $stmt->rowCount();
  }

  // Eliminar un entrenamiento
  public function deleteTraining($id)
  {
    $db = $this->db->getConnection();
    $stmt = $db->prepare('DELETE FROM training_classes WHERE id = :id');
    $stmt->execute([
      'id' => $id
    ]);
  }

  // Obtener los miembros de un entrenamiento
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
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Añadir miembro a una clase
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

  // Eliminar miembro de una clase
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
