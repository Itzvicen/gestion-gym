<?php

class MemberEditController
{
  private $twig;
  private $db;
  private $session;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
    $this->session = Session::getInstance();
  }

  public function edit($memberId)
  {
    $id = $this->session->get('id');

    // Obtener información del usuario
    $user = User::getUserById($id, $this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');
    $last_login = $last_login_time . ' desde ' . $last_login_location . ' (' . $last_login_ip . ')';

    // Obtener información del miembro
    $member = Member::getMemberById($memberId, $this->db);
    $classes = Training::getTrainingsByMemberId($memberId, $this->db);

    // Obtener pagos del miembro
    $payments = Payment::getPaymentsByMemberId($memberId, $this->db);

    $updateMessage = $this->session->get('update_success') ?? $this->session->get('update_error') ?? '';
    $this->session->remove('update_success');
    $this->session->remove('update_error');

    echo $this->twig->render('edit-member.twig', [
      'member' => $member,
      'payments' => $payments,
      'avatar' => $avatar_fallback,
      'full_name' => $full_name,
      'updateMessage' => $updateMessage,
      'classes' => $classes,
      'last_login' => $last_login
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

      // Validar datos
      $errors = [];

      $requiredFields = [
        'first_name' => 'El nombre',
        'last_name' => 'El apellido',
        'email' => 'El correo electrónico',
        'phone' => 'El teléfono',
        'registration_date' => 'La fecha de registro',
        'birth_date' => 'La fecha de nacimiento'
      ];

      foreach ($requiredFields as $field => $fieldName) {
        if (empty($_POST[$field])) {
          $errors[] = $fieldName . ' es requerido.';
        }
      }

      // Validación adicional para el correo electrónico
      if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El correo electrónico no es válido.';
      }

      if (!empty($errors)) {
        // Mostrar los errores al usuario
        foreach ($errors as $error) {
          echo $error . '<br>';
        }
        return;
      }

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
        $this->session->set('update_success', 'Actualización realizada con éxito.');
      } else {
        $this->session->set('update_error', 'Error al actualizar.');
      }

      // Redirigir o mostrar un mensaje de éxito
      header('Location: /dashboard/edit/' . $memberId);
    }
  }

  public function delete($memberId)
  {
    $deleteCount = Member::deleteMember($memberId, $this->db);

    if ($deleteCount == 0) {
      $this->session->set('delete_error', 'Error al eliminar.');
    } else {
      $this->session->set('delete_success', 'Miembro eliminado con éxito.');
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
