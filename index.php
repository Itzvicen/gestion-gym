<?php
// Incluir la configuración de la base de datos y Twig
require_once 'config/database.php';
$twig = require 'config/twig.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

// Incluir modelos y controladores
require_once 'models/User.php';
require_once 'models/Member.php';
require_once 'models/TokenCsrf.php';
require_once 'models/Payment.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/MemberEditController.php';
require_once 'controllers/MemberCreateController.php';
require_once 'controllers/PaymentController.php';
require_once 'controllers/LogoutController.php';
require_once 'controllers/AccountController.php';

// Crear instancias de modelos
$userModel = new User($pdo);

// Crear instancias de controladores
$loginController = new LoginController($twig, $pdo);
$dashboardController = new DashboardController($twig, $userModel, $db); // Asegúrate de pasar los parámetros correctos
$paymentController = new PaymentController($twig, $db);
$memberEditController = new MemberEditController($twig, $db);
$memberCreateController = new MemberCreateController($twig, $db);
$accountController = new AccountController($twig, $db);
$logoutController = new LogoutController();

// Enrutamiento
$request = $_SERVER['REQUEST_URI'];

switch ($request) {
  case '/logout':
    $logoutController->logout();
    break;
  case '/':
    $loginController->index();
    break;
  case '/dashboard':
    $dashboardController->index();
    break;
  case (preg_match("/^\/dashboard\/edit\/(\d+)$/", $request, $matches) ? true : false):
    $memberEditController->edit($matches[1]);
    break;
  case '/dashboard/member/create':
    $memberCreateController->createMember();
    break;
  case (preg_match("/^\/dashboard\/member\/(\d+)\/delete$/", $request, $matches) ? true : false):
    $memberEditController->delete($matches[1]);
    break;
  case (preg_match("/^\/dashboard\/member\/update\/(\d+)$/", $request, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $memberEditController->update($matches[1]);
    break;
  case '/dashboard/payments':
    $paymentController->showPayments();
    break;
  case '/dashboard/payments/create':
    $paymentController->createPayment();
    break;
  case (preg_match("/^\/dashboard\/search/", $request) ? true : false):
    $dashboardController->searchMembers();
    break;
  case (preg_match("/^\/dashboard\/order/", $request) ? true : false):
    $dashboardController->sortMembers();
    break;
  // Ordenar pagos
  case (preg_match("/^\/dashboard\/payments\/order/", $request) ? true : false):
    $paymentController->sortPayments();
    break;
  case '/profile':
    $accountController->editAccount();
    break;
  case (preg_match("/^\/account\/update/", $request) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $accountController->updateAccount();
    break;
  default:
    http_response_code(404);
    echo $twig->render('404.twig');
    break;
}
