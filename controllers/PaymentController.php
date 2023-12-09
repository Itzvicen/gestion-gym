<?php

class PaymentController
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

  public function showPayments()
  {
    $id = $this->session->get('id');
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener información del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener los pagos
    $payments = Payment::getAllPayments($this->db);
    // Obtener los miembros
    $members = Member::getAllMembers($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    // Mensajes de Whatsapp y actualización de pagos
    $whatsappSendMessage = $this->session->get('whatsapp_send') ?? $this->session->get('whatsapp_send_error') ?? '';
    $paymentUpdatedMessage = $this->session->get('payment_updated') ?? $this->session->get('payment_updated_error') ?? '';

    $this->session->remove('whatsapp_send');
    $this->session->remove('whatsapp_send_error');
    $this->session->remove('payment_updated');
    $this->session->remove('payment_updated_error');

    echo $this->twig->render('payments.twig', [
      'payments' => $payments,
      'full_name' => $full_name,
      'currentUrl' => $currentUrl,
      'members' => $members,
      'avatar' => $avatar_fallback,
      'whatsappSendMessage' => $whatsappSendMessage,
      'paymentUpdatedMessage' => $paymentUpdatedMessage
    ]);
  }

  public function createPayment()
  {
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
      echo $this->twig->render('create-payment.twig');
    }
  }

  public function sortPayments()
  {
    $id = $this->session->get('id');
    $currentUrl = $_SERVER['REQUEST_URI'];
    $order = $_GET['by'] ?? 'default';

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener pagos
    $payments = Payment::getOrderedPayments($order, $this->db);
    $members = Member::getAllMembers($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user->getFirstName() . ' ' . $user->getLastName();
    $avatar_fallback = $user->getInitials();

    echo $this->twig->render('payments.twig', [
      'payments' => $payments,
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'members' => $members
    ]);
  }

  public function updatePayment($paymentId)
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Recoger los datos del formulario
      $payment_status = $_POST['payment_status'];

      // Actualizar el pago
      $rowCount = Payment::updatePayment($this->db, $paymentId, $payment_status);

      if ($rowCount > 0) {
        $this->session->set('payment_updated', 'Estado del pago actualizado correctamente');
      } else {
        $this->session->set('payment_updated_error', 'Error al actualizar el pago');
      }

      // Redirigir al usuario a la página de pagos
      header('Location: /dashboard/payments');
      exit;
    } else {
      // Mostrar el formulario de actualización de pagos
      echo $this->twig->render('payments.twig');
    }
  }

  public function sendPaymentReminder($paymentId)
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Recoger los datos del formulario
      $member_id = $_POST['member_id'];
      // Mandar recordatorio de pago al miembro por whatsapp
      $result = Payment::sendPaymentReminder($this->db, $member_id, $paymentId);

      if ($result) {
        $this->session->set('whatsapp_send', 'Recordatorio de pago enviado correctamente');
      } else {
        $this->session->set('whatsapp_send_error', 'Error al enviar el recordatorio de pago');
      }

      // Redirigir al usuario a la página de pagos
      header('Location: /dashboard/payments');
      exit;
    }
  }
}
