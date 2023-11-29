<?php

class DashboardController
{
  private $twig;
  private $userModel;
  private $db;
  
  public function __construct($twig, $userModel, $db) {
      $this->twig = $twig;
      $this->userModel = $userModel;
      $this->db = $db;
  }

  public function index() {
    if (!isset($_SESSION['username'])) {
      header('Location: /');
      exit;
    }

    $username = $_SESSION['username'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener las 2 primeras letras del nombre de usuario
    $avatar_fallback = substr($username, 0, 2);
    // Obtener todos los miembros
    $members = Member::getAllMembers($this->db);
    // Obtener los miembros activos
    $active_members = Member::getActiveMembers($this->db);
    // Obtener los miembros inactivos
    $inactive_members = Member::getInactiveMembers($this->db);
    // Obtener el total de pagos
    $total_payments = Payment::getTotalPayments($this->db);
    // Obtener los pagos no pagados
    $unpaid_payments = Payment::getUnpaidPayments($this->db);

    echo $this->twig->render('dashboard.twig', [
      'username' => $username,
      'avatar' => $avatar_fallback,
      'active_members' => $active_members,
      'inactive_members' => $inactive_members,
      'total_payments' => $total_payments,
      'unpaid_payments' => $unpaid_payments,
      'members' => $members,
      'currentUrl' => $currentUrl
    ]);
  }

  public function searchMembers() {
    if (!isset($_SESSION['username'])) {
        header('Location: /');
        exit;
    }

    $username = $_SESSION['username'];
    $currentUrl = $_SERVER['REQUEST_URI'];
    $searchQuery = $_GET['query'] ?? '';
    $isSearching = !empty($_GET['query']);

    $deleteMessage = '';
    if (isset($_SESSION['delete_success'])) {
      $deleteMessage = $_SESSION['delete_success'];
      unset($_SESSION['delete_success']);
    } else if (isset($_SESSION['delete_error'])) {
      $deleteMessage = $_SESSION['delete_error'];
      unset($_SESSION['delete_error']);
    }

    // Obtener las 2 primeras letras del nombre de usuario
    $avatar_fallback = substr($username, 0, 2);
    // Obtener todos los miembros
    $members = Member::getAllMembers($this->db);
    // Obtener los miembros activos
    $active_members = Member::getActiveMembers($this->db);
    // Obtener los miembros inactivos
    $inactive_members = Member::getInactiveMembers($this->db);
    // Obtener el total de pagos
    $total_payments = Payment::getTotalPayments($this->db);
    // Obtener los pagos no pagados
    $unpaid_payments = Payment::getUnpaidPayments($this->db);
    // Obtener los miembros que coinciden con la consulta de búsqueda
    $members = Member::searchByName($searchQuery, $this->db);

    echo $this->twig->render('dashboard.twig', [
      'username' => $username,
      'avatar' => $avatar_fallback,
      'active_members' => $active_members,
      'inactive_members' => $inactive_members,
      'total_payments' => $total_payments,
      'unpaid_payments' => $unpaid_payments,
      'members' => $members,
      'currentUrl' => $currentUrl,
      'isSearching' => $isSearching,
      'searchQuery' => $_GET['query'] ?? '',
      'deleteMessage' => $deleteMessage
    ]);
}

}
