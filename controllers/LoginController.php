<?php
session_start();

class LoginController
{
  private $userModel;
  private $twig;

  public function __construct($twig, $db)
  {
    $this->userModel = new User($db);
    $this->twig = $twig;
  }

  public function index()
  {
    $token = CsrfToken::generateToken();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!CsrfToken::validateToken($_POST['csrf_token'])) {
        die('Invalid CSRF token');
      }

      $username = $_POST['username'];
      $password = $_POST['password'];

      $result = $this->userModel->checkCredentials($username, $password);

      if ($result) {
        $_SESSION['username'] = $username;
        $_SESSION['id'] = $result['id'];
        $_SESSION['first_name'] = $result['first_name'];
        $_SESSION['last_name'] = $result['last_name'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['email'] = $result['email'];
        header('Location: /dashboard');
        exit;
      } else {
        $error = 'Nombre de usuario o contraseña incorrectos';
        echo $this->twig->render('login.twig', ['error' => $error, 'token' => $token]);
      }
    } else {
      echo $this->twig->render('login.twig', ['token' => $token]);
    }
  }
}
