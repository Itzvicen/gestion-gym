<?php 

class Payment {
  private $id;
  private $member_id;
  private $amount;
  private $payment_date;
  private $payment_method;
  private $payment_status;

  public function __construct($id, $member_id, $amount, $payment_date, $payment_method, $payment_status) {
    $this->id = $id;
    $this->member_id = $member_id;
    $this->amount = $amount;
    $this->payment_date = $payment_date;
    $this->payment_method = $payment_method;
    $this->payment_status = $payment_status;
  }

  public function getId() {
    return $this->id;
  }

  public function getMemberId() {
    return $this->member_id;
  }

  public function getAmount() {
    return $this->amount;
  }

  public function getPaymentDate() {
    return $this->payment_date;
  }

  public function getPaymentMethod() {
    return $this->payment_method;
  }

  public function getPaymentStatus() {
    return $this->payment_status;
  }

  // Obtener todos los pagos
  public static function getAllPayments($db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("
      SELECT p.*, m.first_name, m.last_name 
      FROM payments p 
      JOIN members m ON p.member_id = m.id
      ");
    $stmt->execute();
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $payments;
  }

  // Obtener un pago por id de miembro
  public static function getPaymentsByMemberId($memberId, $db) {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM payments WHERE member_id = :member_id');
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Crear un nuevo pago
  public static function createPayment($db, $member_id, $amount, $payment_method, $payment_status) {
    $db = $db->getConnection();
    $stmt = $db->prepare("
      INSERT INTO payments (member_id, amount, payment_method, payment_status) 
      VALUES (:member_id, :amount, :payment_method, :payment_status)
      ");
    $stmt->bindParam(':member_id', $member_id);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':payment_status', $payment_status);
    $stmt->execute();
  }

  // Actualizar el estado de un pago
  public static function updatePayment($db, $payment_id, $payment_status) {
    $db = $db->getConnection();
    $stmt = $db->prepare("
      UPDATE payments
      SET payment_status = :payment_status
      WHERE id = :payment_id
      ");
    $stmt->bindParam(':payment_status', $payment_status);
    $stmt->bindParam(':payment_id', $payment_id);
    $stmt->execute();

    return $stmt->rowCount();
  }

  // Método estático para obtener todos los pagos por ordenacion
  public static function getOrderedPayments($order, $db) {
    $db = $db->getConnection();
    switch ($order) {
      case 'recent':
        $stmt = $db->prepare('SELECT p.*, m.first_name, m.last_name FROM payments p JOIN members m ON p.member_id = m.id ORDER BY payment_date DESC');
        break;
      case 'old':
          $stmt = $db->prepare('SELECT p.*, m.first_name, m.last_name FROM payments p JOIN members m ON p.member_id = m.id ORDER BY payment_date ASC');
          break;
      case 'total_asc':
          $stmt = $db->prepare('SELECT p.*, m.first_name, m.last_name FROM payments p JOIN members m ON p.member_id = m.id ORDER BY amount ASC');
          break;
      case 'total_desc':
          $stmt = $db->prepare('SELECT p.*, m.first_name, m.last_name FROM payments p JOIN members m ON p.member_id = m.id ORDER BY amount DESC');
          break;
      default:
          $stmt = $db->prepare('SELECT p.*, m.first_name, m.last_name FROM payments p JOIN members m ON p.member_id = m.id');
          break;
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Obtener la suma de pagos de los últimos 30 días
  public static function getTotalPayments($db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE payment_status = 'Pagado' AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $total_payments = $stmt->fetchColumn();

    return $total_payments;
  }

  // Obtener los pagos no pagados
  public static function getUnpaidPayments($db) {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE payment_status = 'Impagado' AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $unpaid_payments = $stmt->fetchColumn();

    return $unpaid_payments;
  }

  // Mandar recordatorio de pago al miembro por whatsapp
  public static function sendPaymentReminder($db, $member_id, $paymentId) {
    $db = $db->getConnection();
    // Obtener el miembro
    $stmt = $db->prepare("SELECT * FROM members WHERE id = :member_id");
    $stmt->bindParam(':member_id', $member_id);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener información del pago
    $stmt = $db->prepare("SELECT * FROM payments WHERE id = :payment_id");
    $stmt->bindParam(':payment_id', $paymentId);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Enviar mensaje de WhatsApp al miembro
    $prefijo = '34';
    $to = $prefijo . $member['phone'];
    $firstName = $member['first_name'];
    $lastName = $member['last_name'];
    $paymentAmount = $payment['amount'];

    $whatsappSender = new WhatsAppSender('https://graph.facebook.com/v18.0/176913302172866/messages', $_ENV['WHATSAPP_TOKEN']);
    $messageSent = $whatsappSender->sendPaymentReminder($to, $firstName, $lastName, $paymentAmount);

    return $messageSent;
  }
}