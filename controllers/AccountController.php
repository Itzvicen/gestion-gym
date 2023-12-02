<?php

class AccountController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function editAccount()
  {
    // Obtener información del usuario actual
    $username = $_SESSION['username'];
    $avatar_fallback = substr($username, 0, 2);
    $currentUrl = $_SERVER['REQUEST_URI'];
    $userId = $_SESSION['id'];
    $user = User::getUserById($userId, $this->db);
    $userData = $user[0];
    $avatar_fallback = substr($username, 0, 2);

    // Obtener información del formulario
    $first_name = $userData['first_name'];
    $last_name = $userData['last_name'];
    $email = $userData['email'];

    $passwordMessage = '';
    if (isset($_SESSION['password_success'])) {
      $passwordMessage = $_SESSION['password_success'];
      unset($_SESSION['password_success']);
    } else if (isset($_SESSION['password_error'])) {
      $passwordMessage = $_SESSION['password_error'];
      unset($_SESSION['password_error']);
    }

    echo $this->twig->render('profile.twig', [
      'user' => $userData,  // Pass the entire user data for reference
      'username' => $username,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'first_name' => $first_name,
      'last_name' => $last_name,
      'email' => $email,
      'passwordMessage' => $passwordMessage,
      'avatar' => $avatar_fallback
    ]);
  }

  public function updateAccount()
  {
    // Obtener información del usuario actual
    $username = $_SESSION['username'];
    $avatar_fallback = substr($username, 0, 2);
    $userId = $_SESSION['id'];
    $user = User::getUserById($userId, $this->db);

    // Obtener información del formulario
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    // Validar que el nombre no esté vacío
    if (empty($name)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'username' => $username,
        'avatar' => $avatar_fallback,
        'error' => 'El nombre no puede estar vacío'
      ]);
      return;
    }

    // Validar que el email no esté vacío
    if (empty($email)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'username' => $username,
        'avatar' => $avatar_fallback,
        'error' => 'El email no puede estar vacío'
      ]);
      return;
    }

    // Validar que el email sea válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'username' => $username,
        'avatar' => $avatar_fallback,
        'error' => 'El email no es válido'
      ]);
      return;
    }

    // Validar que la contraseña no esté vacía
    if (empty($password)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'username' => $username,
        'avatar' => $avatar_fallback,
        'error' => 'La contraseña no puede estar vacía'
      ]);
      return;
    }

    // Validar que la contraseña tenga al menos 6 caracteres
    if (strlen($password) < 6) {
      echo $this->twig
        ->render('profile.twig', [
          'user' => $user,
          'username' => $username,
          'avatar' => $avatar_fallback,
          'error' => 'La contraseña debe tener al menos 6 caracteres'
        ]);
      return;
    }

    // Validar que la contraseña y la confirmación sean iguales
    if ($password !== $password_confirmation) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'username' => $username,
        'avatar' => $avatar_fallback,
        'error' => 'La contraseña y la confirmación no coinciden'
      ]);
      return;
    }

    // Actualizar el usuario en la base de datos
    User::updateUser($userId, $first_name, $last_name, $email, $password, $this->db);

    // Redirigir al usuario a la página de inicio
    header('Location: /');
  }

  public function changePassword()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Obtener la información del formulario
      $current_password = $_POST['current_password'];
      $new_password = $_POST['new_password'];
      $confirm_new_password = $_POST['confirm_new_password'];

      // Obtener la información del usuario actual
      $userId = $_SESSION['id'];
      $user = User::getUserById($userId, $this->db);
      $userData = $user[0];

      // Verificar que la contraseña actual sea correcta
      if (!password_verify($current_password, $userData['password'])) {
        $_SESSION['password_error'] = 'La contraseña actual es incorrecta';
        header('Location: /profile');
        exit;
      }

      // Verificar que la nueva contraseña y la confirmación sean iguales
      if ($new_password !== $confirm_new_password) {
        $_SESSION['password_error'] = 'La nueva contraseña y la confirmación no coinciden';
        header('Location: /profile');
        exit;
      }

      // Actualizar la contraseña en la base de datos
      $result = User::updatePassword($userId, $new_password, $this->db);

      if ($result > 0) {
        $_SESSION['password_success'] = 'Contraseña actualizada correctamente';
      } else {
        $_SESSION['password_error'] = 'Error al actualizar la contraseña';
      }

      // Redirigir al usuario a la página de perfil
      header('Location: /profile');
      exit;
    } else {
      // Mostrar el formulario de cambio de contraseña
      $this->editAccount();
    }
  }
}
