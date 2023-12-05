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
    $id = $_SESSION['id'];

    // Obtener información del usuario
    $user = User::getUserById($id, $this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user[0]['first_name'] . ' ' . $user[0]['last_name'];
    $avatar_fallback = substr($full_name, 0, 2);

    // Obtener información del miembro
    $memberData = Member::getMemberById($memberId, $this->db);

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

    // Obtener pagos del miembro
    $payments = Payment::getPaymentsByMemberId($memberId, $this->db);

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
      'full_name' => $full_name,
      'updateMessage' => $updateMessage
    ]);
  }

  public function update($memberId)
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Obtener datos del formulario
      // Manejar la carga de la imagen
      $imagePath = $this->handleImageUpload();

      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];
      $registration_date = $_POST['registration_date'];
      $birth_date = $_POST['birth_date'];
      $active = $_POST['active'];

      // Validar datos aquí...

      // Array con los datos del miembro
      $memberData = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'registration_date' => $registration_date,
        'birth_date' => $birth_date,
        'active' => $active,
        'image_path' => $imagePath
      ];

      // Actualizar en la base de datos
      $success = Member::updateMember($memberId, $memberData, $this->db);

      if ($success) {
        $_SESSION['update_success'] = 'Actualización realizada con éxito.';
      } else {
        $_SESSION['update_error'] = 'Error al actualizar.';
      }

      // Redirigir o mostrar un mensaje de éxito
      header('Location: /dashboard/edit/' . $memberId);
    }
  }

  public function delete($memberId)
  {
    $deleteCount = Member::deleteMember($memberId, $this->db);

    if ($deleteCount == 0) {
      $_SESSION['delete_error'] = 'Error al eliminar.';
    } else {
      $_SESSION['delete_success'] = 'Miembro eliminado con éxito.';
    }

    header('Location: /dashboard');
  }

  private function handleImageUpload()
  {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $image = $_FILES['image'];
      $targetDir = "uploads/pictures/";
      $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
      $newFileName = uniqid() . '.' . $extension;
      $imagePath = $targetDir . $newFileName;

      if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
      }

      move_uploaded_file($image['tmp_name'], $imagePath);
      return $imagePath;
    }

    return 'uploads/pictures/default.png';
  }
}
