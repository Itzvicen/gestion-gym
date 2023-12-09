<?php

class DashboardController
{
  private $twig;
  private $db;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
  }

  public function index()
  {
    if (!isset($_SESSION['username'])) {
      header('Location: /');
      exit;
    }

    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
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

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    $deleteMessage = $_SESSION['delete_success'] ?? $_SESSION['delete_error'] ?? '';
    unset($_SESSION['delete_success'], $_SESSION['delete_error']);

    $createMessage = $_SESSION['create_success'] ?? $_SESSION['create_error'] ?? '';
    unset($_SESSION['create_success'], $_SESSION['create_error']);


    echo $this->twig->render('dashboard.twig', [
      'avatar' => $avatar_fallback,
      'full_name' => $full_name,
      'active_members' => $active_members,
      'inactive_members' => $inactive_members,
      'total_payments' => $total_payments,
      'unpaid_payments' => $unpaid_payments,
      'members' => $members,
      'currentUrl' => $currentUrl,
      'deleteMessage' => $deleteMessage,
      'createMessage' => $createMessage
    ]);
  }

  public function searchMembers()
  {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $searchQuery = $_GET['query'] ?? '';
    $isSearching = !empty($_GET['query']);
    $id = $_SESSION['id'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
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

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    echo $this->twig->render('dashboard.twig', [
      'avatar' => $avatar_fallback,
      'full_name' => $full_name,
      'active_members' => $active_members,
      'inactive_members' => $inactive_members,
      'total_payments' => $total_payments,
      'unpaid_payments' => $unpaid_payments,
      'members' => $members,
      'currentUrl' => $currentUrl,
      'isSearching' => $isSearching,
      'searchQuery' => $_GET['query'] ?? ''
    ]);
  }

  public function sortMembers()
  {
    if (!isset($_SESSION['username'])) {
      header('Location: /');
      exit;
    }

    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];
    $order = $_GET['by'] ?? 'default';

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener todos los miembros
    $members = Member::getOrderedMembers($order, $this->db);
    // Obtener los miembros activos
    $active_members = Member::getActiveMembers($this->db);
    // Obtener los miembros inactivos
    $inactive_members = Member::getInactiveMembers($this->db);
    // Obtener el total de pagos
    $total_payments = Payment::getTotalPayments($this->db);
    // Obtener los pagos no pagados
    $unpaid_payments = Payment::getUnpaidPayments($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    echo $this->twig->render('dashboard.twig', [
      'avatar' => $avatar_fallback,
      'full_name' => $full_name,
      'active_members' => $active_members,
      'inactive_members' => $inactive_members,
      'total_payments' => $total_payments,
      'unpaid_payments' => $unpaid_payments,
      'members' => $members,
      'currentUrl' => $currentUrl,
      'order' => $order
    ]);
  }
}
