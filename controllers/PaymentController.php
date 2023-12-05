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
    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener los pagos
    $payments = Payment::getAllPayments($this->db);
    $members = Member::getAllMembers($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user[0]['first_name'] . ' ' . $user[0]['last_name'];
    $avatar_fallback = substr($full_name, 0, 2);
    $whatsappSendMessage = '';
    if (isset($_SESSION['whatsapp_send'])) {
      $whatsappSendMessage = $_SESSION['whatsapp_send'];
      unset($_SESSION['whatsapp_send']);
    } else if (isset($_SESSION['whatsapp_send_error'])) {
      $whatsappSendMessage = $_SESSION['whatsapp_send_error'];
      unset($_SESSION['whatsapp_send_error']);
    }

    $paymentUpdatedMessage = '';
    if (isset($_SESSION['payment_updated'])) {
      $paymentUpdatedMessage = $_SESSION['payment_updated'];
      unset($_SESSION['payment_updated']);
    } else if (isset($_SESSION['payment_updated_error'])) {
      $paymentUpdatedMessage = $_SESSION['payment_updated_error'];
      unset($_SESSION['payment_updated_error']);
    }

    echo $this->twig->render('payments.twig', [
      'payments' => $payments,
      'full_name' => $full_name,
      'avatar' => $avatar_fallback,
      'currentUrl' => $currentUrl,
      'members' => $members,
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
      echo $this->twig->render('create_payment.twig');
    }
  }

  public function sortPayments()
  {
    $id = $_SESSION['id'];
    $currentUrl = $_SERVER['REQUEST_URI'];
    $order = $_GET['by'] ?? 'default';

    // Obtener infomarcion del usuario
    $user = User::getUserById($id, $this->db);
    // Obtener pagos
    $payments = Payment::getOrderedPayments($order, $this->db);
    $members = Member::getAllMembers($this->db);

    // Obtener las 2 primeras letras del nombre
    $full_name = $user[0]['first_name'] . ' ' . $user[0]['last_name'];
    $avatar_fallback = substr($full_name, 0, 2);

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

      // Validar los datos aquí...

      // Actualizar el pago
      $rowCount = Payment::updatePayment($this->db, $paymentId, $payment_status);

      if ($rowCount > 0) {
        $_SESSION['payment_updated'] = 'Estado del pago actualizado correctamente';
      } else {
        $_SESSION['payment_updated_error'] = 'Error al actualizar el pago';
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
        $_SESSION['whatsapp_send'] = 'Recordatorio de pago enviado correctamente';
      } else {
        $_SESSION['whatsapp_send_error'] = 'Error al enviar el recordatorio de pago';
      }

      // Redirigir al usuario a la página de pagos
      header('Location: /dashboard/payments');
      exit;
    } else {
      // Mostrar el formulario de envío de recordatorio de pago
      echo $this->twig->render('send_payment_reminder.twig');
    }
  }
}
