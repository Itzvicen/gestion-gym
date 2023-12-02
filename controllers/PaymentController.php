<?php

class PaymentController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function showPayments()
  {
    if (!isset($_SESSION['username'])) {
      header('Location: /');
      exit;
    } else {
      $username = $_SESSION['username'];
    }

    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener las 2 primeras letras del nombre de usuario
    $avatar_fallback = substr($username, 0, 2);
    $payments = Payment::getAllPayments($this->db);
    $members = Member::getAllMembers($this->db);

    echo $this->twig->render('payments.twig', [
      'payments' => $payments,
      'username' => $username,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'members' => $members
    ]);
  }

  public function createPayment() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Recoger los datos del formulario
      $member_id = $_POST['member_id'];
      $amount = $_POST['amount'];
      $payment_method = $_POST['payment_method'];
      $payment_status = $_POST['payment_status'];

      // Validar los datos aquí...

      // Crear el pago
      Payment::createPayment($this->db, $member_id, $amount, $payment_method, $payment_status);

      // Redirigir al usuario a la página de pagos
      header('Location: /dashboard/payments');
      exit;
    } else {
      // Mostrar el formulario de creación de pagos
      echo $this->twig->render('create_payment.twig');
    }
  }

  public function sortPayments() {
    if (!isset($_SESSION['username'])) {
        header('Location: /');
        exit;
    }

    $username = $_SESSION['username'];
    $currentUrl = $_SERVER['REQUEST_URI'];
    $order = $_GET['by'] ?? 'default';

    // Obtener las 2 primeras letras del nombre de usuario
    $avatar_fallback = substr($username, 0, 2);
    // Obtener pagos
    $payments = Payment::getOrderedPayments($order, $this->db);
    $members = Member::getAllMembers($this->db);

    echo $this->twig->render('payments.twig', [
      'payments' => $payments,
      'username' => $username,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'members' => $members
    ]);
  }
}
