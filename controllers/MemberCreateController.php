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

  public function showCreateForm() {
    echo $this->twig->render('create_member.twig'); 
  }

  public function createMember() {
    // Comprobar si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Recoger los datos del formulario
      $firstName = $_POST['first_name'];
      $lastName = $_POST['last_name'];
      $email = $_POST['email'];
      $phone = $_POST['phone'];
      $registrationDate = $_POST['registration_date'];
      $birthDate = $_POST['birth_date'];
      $isActive = $_POST['active'];


      // Si se ha subido una imagen, manejarla aquí
      $imagePath = $this->handleImageUpload();

      // Insertar el nuevo miembro en la base de datos
      $stmt = $this->db->prepare("INSERT INTO members (first_name, last_name, email, phone, registration_date, birth_date, active, image_path) VALUES (:first_name, :last_name, :email, :phone, :registration_date, :birth_date, :active, :image_path)");
      $stmt->execute([
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'phone' => $phone,
        'registration_date' => $registrationDate,
        'birth_date' => $birthDate,
        'active' => $isActive,
        'image_path' => $imagePath
      ]);

      // Redireccionar al usuario, por ejemplo, a la página de lista de miembros
      header('Location: /dashboard');
    }
  }

  private function handleImageUpload() {
    // Código para manejar la subida de imagen
    return "imagen.jpg";
  }
}
