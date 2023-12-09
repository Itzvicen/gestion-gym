<?php

class LoginController
{
  private $userModel;
  private $twig;
  private $session;

  public function __construct($twig, $db)
  {
    $this->userModel = new User($db);
    $this->twig = $twig;
    $this->session = Session::getInstance();
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
        $this->session->set('username', $username);
        $this->session->set('id', $result['id']);
        $this->session->set('first_name', $result['first_name']);
        $this->session->set('last_name', $result['last_name']);
        $this->session->set('role', $result['role']);
        $this->session->set('email', $result['email']);
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