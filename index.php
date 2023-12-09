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
require_once 'models/Training.php';
require_once 'models/WhatsappSender.php';
require_once 'controllers/LoginController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/MemberEditController.php';
require_once 'controllers/MemberCreateController.php';
require_once 'controllers/PaymentController.php';
require_once 'controllers/LogoutController.php';
require_once 'controllers/AccountController.php';
require_once 'controllers/TrainingController.php';

// Crear instancias de modelos

// Crear instancias de controladores
$loginController = new LoginController($twig, $pdo);
$dashboardController = new DashboardController($twig, $db);
$paymentController = new PaymentController($twig, $db);
$memberEditController = new MemberEditController($twig, $db);
$memberCreateController = new MemberCreateController($twig, $db);
$accountController = new AccountController($twig, $db);
$trainingController = new TrainingController($twig, $db);
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
  case (preg_match("/^\/dashboard\/payments\/reminder\/(\d+)$/", $request, $matches) ? true : false):
    $paymentController->sendPaymentReminder($matches[1]);
    break;
  // Actualizar estado de un pago
  case (preg_match("/^\/dashboard\/payments\/update\/(\d+)$/", $request, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $paymentController->updatePayment($matches[1]);
    break;
  case (preg_match("/^\/dashboard\/search/", $request) ? true : false):
    $dashboardController->searchMembers();
    break;
  case (preg_match("/^\/dashboard\/order/", $request) ?  true : false):
    $dashboardController->sortMembers();
    break;
  case (preg_match("/^\/dashboard\/payments\/order/", $request) ? true : false):
    $paymentController->sortPayments();
    break;
  case '/profile':
    $accountController->editAccount();
    break;
  case '/profile/update':
    $accountController->updateAccount();
    break;
  case (preg_match("/^\/profile\/change-password/", $request) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $accountController->changePassword();
    break;
  case '/dashboard/trainings':
    $trainingController->index();
    break;
  case '/dashboard/trainings/create':
    $trainingController->create();
    break;
  case (preg_match("/^\/dashboard\/trainings\/(\d+)\/delete$/", $request, $matches) ? true : false):
    $trainingController->delete($matches[1]);
    break;
  case (preg_match("/^\/dashboard\/trainings\/(\d+)\/update$/", $request, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $trainingController->update($matches[1]);
    break;
  case (preg_match("/^\/dashboard\/training\/(\d+)$/", $request, $matches) ? true : false):
    $trainingController->view($matches[1]);
    break;
  case (preg_match("/^\/dashboard\/training\/(\d+)\/add-member$/", $request, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $trainingController->addMemberToTraining($matches[1]);
    break;
  case (preg_match("/^\/dashboard\/training\/(\d+)\/remove-member$/", $request, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $trainingController->deleteMemberFromClass($matches[1]);
    break;
  default:
    http_response_code(404);
    echo $twig->render('404.twig');
    break;
}
