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
    // Obtener la ubicacion del usuario por su IP
    function getGeoLocation($ip) {
      $url = "http://ip-api.com/json/{$ip}";
      $json = file_get_contents($url);
      $data = json_decode($json);
      $city = property_exists($data, 'city') ? $data->city : 'Desconocido';
      $region = property_exists($data, 'regionName') ? $data->region : 'Desconocido';
      $countryCode = property_exists($data, 'countryCode') ? $data->countryCode : 'Desconocido';
      return $city . ', ' . $region . ', ' . $countryCode;
    }

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
        $this->session->set('last_login_time', date('H:i:s'));
        $this->session->set('last_login_ip', $_SERVER['REMOTE_ADDR']);
        $this->session->set('last_login_location', getGeoLocation($_SERVER['REMOTE_ADDR']));
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
