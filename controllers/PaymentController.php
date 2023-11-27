<?php

class PaymentController {
  private $twig;
  private $db;

  public function __construct($twig, $db) {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function showPayments() {
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
    echo $this->twig->render('payments.twig', [
      'payments' => $payments,
      'username' => $username,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl
    ]);
  }
}
