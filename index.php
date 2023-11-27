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
require_once 'controllers/PaymentController.php';
require_once 'controllers/LogoutController.php';

// Crear instancias de modelos
$userModel = new User($pdo);

// Crear instancias de controladores
$loginController = new LoginController($twig, $pdo);
$dashboardController = new DashboardController($twig, $userModel, $db); // Asegúrate de pasar los parámetros correctos
$paymentController = new PaymentController($twig, $db);
$memberEditController = new MemberEditController($twig, $db);
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
  case (preg_match("/^\/dashboard\/member\/update\/(\d+)$/", $request, $matches) && $_SERVER['REQUEST_METHOD'] === 'POST' ? true : false):
    $memberEditController->update($matches[1]);
    break;
  case '/dashboard/payments':
    $paymentController->showPayments();
    break;
}