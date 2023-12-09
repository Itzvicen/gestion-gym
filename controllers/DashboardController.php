<?php

class DashboardController
{
  private $twig;
  private $db;
  private $session;

  public function __construct($twig, $db)
  {
    $this->twig = $twig;
    $this->db = $db;
    $this->session = Session::getInstance();
  }

  public function index()
  {
    if (!$this->session->get('username')) {
      header('Location: /');
      exit;
    }

    $id = $this->session->get('id');
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

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');

    $deleteMessage = $this->session->get('delete_success') ?? $this->session->get('delete_error') ?? '';
    $this->session->remove('delete_success');
    $this->session->remove('delete_error');

    $createMessage = $this->session->get('create_success') ?? $this->session->get('create_error') ?? '';
    $this->session->remove('create_success');
    $this->session->remove('create_error');


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
      'createMessage' => $createMessage,
      'last_login_time' => $last_login_time,
      'last_login_ip' => $last_login_ip,
      'last_login_location' => $last_login_location
    ]);
  }

  public function searchMembers()
  {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $searchQuery = $_GET['query'] ?? '';
    $isSearching = !empty($_GET['query']);
    $id = $this->session->get('id');

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

    // Informacion ultimo incio de sesion
    $last_login_time = $this->session->get('last_login_time');
    $last_login_ip = $this->session->get('last_login_ip');
    $last_login_location = $this->session->get('last_login_location');

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
      'searchQuery' => $_GET['query'] ?? '',
      'last_login_time' => $last_login_time,
      'last_login_ip' => $last_login_ip,
      'last_login_location' => $last_login_location
    ]);
  }

  public function sortMembers()
  {
    if (!$this->session->get('username')) {
      header('Location: /');
      exit;
    }

    $id = $this->session->get('id');
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
