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
}