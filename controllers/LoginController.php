<?php
session_start();

class LoginController {
  private $userModel;
  private $twig;

  public function __construct($twig, $db) {
      $this->userModel = new User($db);
      $this->twig = $twig;
  }
  
  public function index() {
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
