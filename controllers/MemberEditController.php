<?php

class MemberEditController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function edit($memberId)
  {
    if (!isset($_SESSION['username'])) {
      header('Location: /');
      exit;
    } else {
      $username = $_SESSION['username'];
    }
    // Obtener las 2 primeras letras del nombre de usuario
    $avatar_fallback = substr($username, 0, 2);

    // Obtener información del miembro y pagos
    $db = $this->db->getConnection();
    $stmt = $db->prepare('SELECT * FROM members WHERE id = :id');
    $stmt->bindParam(':id', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    $memberData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$memberData) {
      echo $this->twig->render('404.twig');
      return;
    }

    $member = new Member(
      $memberData['id'],
      $memberData['first_name'],
      $memberData['last_name'],
      $memberData['email'],
      $memberData['phone'],
      $memberData['birth_date'],
      $memberData['registration_date'],
      $memberData['active'],
      $memberData['image_path']
    );

    $stmt = $db->prepare('SELECT * FROM payments WHERE member_id = :member_id');
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateMessage = '';
    if (isset($_SESSION['update_success'])) {
      $updateMessage = $_SESSION['update_success'];
      unset($_SESSION['update_success']);
    } else if (isset($_SESSION['update_error'])) {
      $updateMessage = $_SESSION['update_error'];
      unset($_SESSION['update_error']);
    }

    echo $this->twig->render('edit_member.twig', [
      'member' => $member,
      'payments' => $payments,
      'avatar' => $avatar_fallback,
      'username' => $username,
      'updateMessage' => $updateMessage
    ]);
  }

  public function update($memberId)
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Obtener datos del formulario
      // Manejar la carga de la imagen
      $image = isset($_FILES['image']) ? $_FILES['image'] : null;
      $imagePath = isset($_POST['image_path']) ? $_POST['image_path'] : '';

      if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/pictures/";
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $newFileName = "perfil-" . $memberId . "." . $extension;
        $imagePath = $targetDir . $newFileName;

        // Mover el archivo subido al directorio de destino
        move_uploaded_file($image['tmp_name'], $imagePath);
      } else {
        $imagePath = "uploads/pictures/default.png";
      }

      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];
      $registration_date = $_POST['registration_date'];
      $birth_date = $_POST['birth_date'];
      $active = $_POST['active'];

      // Validar datos aquí...

      // Actualizar en la base de datos
      $db = $this->db->getConnection();
      $stmt = $db->prepare("UPDATE members SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, registration_date = :registration_date, birth_date = :birth_date, active = :active, image_path = :image_path WHERE id = :id");
      $stmt->bindParam(':first_name', $first_name);
      $stmt->bindParam(':last_name', $last_name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':phone', $phone);
      $stmt->bindParam(':registration_date', $registration_date);
      $stmt->bindParam(':birth_date', $birth_date);
      $stmt->bindParam(':active', $active, PDO::PARAM_INT);
      $stmt->bindParam(':image_path', $imagePath);
      $stmt->bindParam(':id', $memberId, PDO::PARAM_INT);
      $stmt->execute();

      $success = $stmt->execute();

      if ($success) {
        $_SESSION['update_success'] = 'Actualización realizada con éxito.';
      } else {
        $_SESSION['update_error'] = 'Error al actualizar.';
      }

      // Redirigir o mostrar un mensaje de éxito
      header('Location: /dashboard/edit/' . $memberId);
    }
  }
}
