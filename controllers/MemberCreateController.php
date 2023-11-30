<?php

class MemberCreateController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function createMember() {
    // Comprobar si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Recoger los datos del formulario
      $firstName = $_POST['first_name'] ?? '';
      $lastName = $_POST['last_name'] ?? '';
      $email = $_POST['email'] ?? '';
      $phone = $_POST['phone'] ?? '';
      $registrationDate = $_POST['registration_date'] ?? '';
      $birthDate = $_POST['birth_date'] ?? '';
      $isActive = $_POST['active'] ?? '0';

      // Si se ha subido una imagen, manejarla aquí
      $imagePath = $this->handleImageUpload();

      // Crear un nuevo miembro
      $memberData = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'birth_date' => $birthDate,
        'registration_date' => $registrationDate,
        'active' => $isActive,
        'image_path' => $imagePath
      ];

      $result = Member::createMember($memberData, $this->db);

      if ($result) {
        $_SESSION['create_success'] = 'Miembro creado correctamente';
      } else {
        $_SESSION['create_error'] = 'Error al crear el miembro';
      }

      // Redirigir al usuario o mostrar un mensaje de éxito/error
      header('Location: /dashboard');
    }
  }

  private function handleImageUpload() {
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
