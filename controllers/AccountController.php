<?php

class AccountController
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

  public function editAccount()
  {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $userId = $this->session->get('id');
    $user = User::getUserById($userId, $this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');
    $last_login = $last_login_time . ' desde ' . $last_login_location . ' (' . $last_login_ip . ')';

    $passwordMessage = $this->session->get('password_success') ?? $this->session->get('password_error') ?? '';
    $this->session->remove('password_success');
    $this->session->remove('password_error');

    $profileMessage = $this->session->get('profile_success') ?? $this->session->get('profile_error') ?? '';
    $this->session->remove('profile_success');
    $this->session->remove('profile_error');

    echo $this->twig->render('profile.twig', [
      'user' => $user,  // Pass the entire user data for reference
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'passwordMessage' => $passwordMessage,
      'profileMessage' => $profileMessage,
      'last_login' => $last_login
    ]);
  }

  public function updateAccount()
  {
    // Obtener información del usuario actual
    $userId = $_SESSION['id'];
    $user = User::getUserById($userId, $this->db);

    // Obtener información del formulario
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');
    $last_login = $last_login_time . ' desde ' . $last_login_location . ' (' . $last_login_ip . ')';

    // Validar que el nombre no esté vacío
    if (empty($first_name)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'full_name' => $full_name,
        'avatar' => $avatar_fallback,
        'error' => 'El nombre no puede estar vacío',
        'last_login' => $last_login
      ]);
      return;
    }

    // Validar que el apellido no esté vacío
    if (empty($last_name)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'full_name' => $full_name,
        'avatar' => $avatar_fallback,
        'error' => 'El apellido no puede estar vacío',
        'last_login' => $last_login
      ]);
      return;
    }

    // Validar que el email no esté vacío
    if (empty($email)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'full_name' => $full_name,
        'avatar' => $avatar_fallback,
        'error' => 'El email no puede estar vacío',
        'last_login' => $last_login
      ]);
      return;
    }

    // Validar que el email sea válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo $this->twig->render('profile.twig', [
        'user' => $user,
        'full_name' => $full_name,
        'avatar' => $avatar_fallback,
        'error' => 'El email no es válido',
        'last_login' => $last_login
      ]);
      return;
    }

    // Actualizar el usuario en la base de datos
    $result = User::updateUser($userId, $first_name, $last_name, $email, $this->db);

    if ($result > 0) {
      $this->session->set('profile_success', 'Usuario actualizado correctamente');
    } else {
      $this->session->set('profile_error', 'Error al actualizar el usuario');
    }

    // Redirigir al usuario a la página de inicio
    header('Location: /profile');
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

      // Verificar que la contraseña actual sea correcta
      if (!password_verify($current_password, $user->getPassword())) {
        $this->session->set('password_error', 'La contraseña actual es incorrecta');
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
        $this->session->set('password_success', 'Contraseña actualizada correctamente');
      } else {
        $this->session->set('password_error', 'Error al actualizar la contraseña');
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
