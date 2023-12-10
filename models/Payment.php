<?php

/**
 * Clase Payment
 *
 * Esta clase representa un pago realizado por un miembro en un gimnasio.
 * Contiene métodos para obtener información sobre los pagos, crear nuevos pagos,
 * actualizar el estado de un pago, obtener pagos ordenados, obtener la suma de pagos
 * de los últimos 30 días, obtener los pagos no pagados y enviar recordatorios de pago
 * a los miembros por WhatsApp.
 */

class Payment
{
  private $id;
  private $member_id;
  private $amount;
  private $payment_date;
  private $payment_method;
  private $payment_status;

  public function __construct($id, $member_id, $amount, $payment_date, $payment_method, $payment_status)
  {
    $this->id = $id;
    $this->member_id = $member_id;
    $this->amount = $amount;
    $this->payment_date = $payment_date;
    $this->payment_method = $payment_method;
    $this->payment_status = $payment_status;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getMemberId()
  {
    return $this->member_id;
  }

  public function getAmount()
  {
    return $this->amount;
  }

  public function getPaymentDate()
  {
    return $this->payment_date;
  }

  public function getPaymentMethod()
  {
    return $this->payment_method;
  }

  public function getPaymentStatus()
  {
    return $this->payment_status;
  }

  /**
   * Obtener todos los pagos
   *
   * Este método obtiene todos los pagos registrados en la base de datos.
   *
   * @param $db La conexión a la base de datos.
   * @return array Los pagos encontrados.
   */
  public static function getAllPayments($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("
        SELECT p.*, m.first_name, m.last_name
        FROM payments p
        JOIN members m ON p.member_id = m.id
        ");
    $stmt->execute();
    $payments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $payments[] = new Payment(
        $row['id'],
        $row['member_id'],
        $row['amount'],
        $row['payment_date'],
        $row['payment_method'],
        $row['payment_status']
      );
    }

    return $payments;
  }

  /**
   * Obtener un pago por id de miembro
   *
   * Este método obtiene los pagos asociados a un miembro específico.
   *
   * @param int $memberId El id del miembro.
   * @param $db La conexión a la base de datos.
   * @return array Los pagos encontrados.
   */
  public static function getPaymentsByMemberId($memberId, $db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare('SELECT * FROM payments WHERE member_id = :member_id');
    $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
    $stmt->execute();
    
    $payments = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $payments[] = new Payment(
        $row['id'],
        $row['member_id'],
        $row['amount'],
        $row['payment_date'],
        $row['payment_method'],
        $row['payment_status']
      );
    }

    return $payments;
  }

  /**
   * Crear un nuevo pago
   *
   * Este método crea un nuevo registro de pago en la base de datos.
   *
   * @param $db La conexión a la base de datos.
   * @param int $member_id El id del miembro.
   * @param float $amount El monto del pago.
   * @param string $payment_method El método de pago.
   * @param string $payment_status El estado del pago.
   * @return void
   */
  public static function createPayment($db, $member_id, $amount, $payment_date, $payment_method, $payment_status)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("
        INSERT INTO payments (member_id, amount, payment_date, payment_method, payment_status)
        VALUES (:member_id, :amount, :payment_date, :payment_method, :payment_status)
        ");
    $stmt->bindParam(':member_id', $member_id);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':payment_date', $payment_date);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':payment_status', $payment_status);
    $stmt->execute();
  }

  /**
   * Actualizar el estado de un pago
   *
   * Este método actualiza el estado de un pago en la base de datos.
   *
   * @param $db La conexión a la base de datos.
   * @param int $payment_id El id del pago.
   * @param string $payment_status El nuevo estado del pago.
   * @return int El número de filas afectadas.
   */
  public static function updatePayment($db, $payment_id, $payment_status)
  {
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

  /**
   * Obtener todos los pagos por ordenación
   *
   * Este método obtiene todos los pagos ordenados según el criterio especificado.
   *
   * @param string $order El criterio de ordenación.
   * @param $db La conexión a la base de datos.
   * @return array Los pagos encontrados.
   */
  public static function getOrderedPayments($order, $db)
  {
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
    
    $payments = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $payments[] = new Payment(
        $row['id'],
        $row['member_id'],
        $row['amount'],
        $row['payment_date'],
        $row['payment_method'],
        $row['payment_status']
      );
    }

    return $payments;
  }

  /**
   * Obtener la suma de pagos de los últimos 30 días
   *
   * Este método obtiene la suma de los pagos realizados en los últimos 30 días.
   *
   * @param $db La conexión a la base de datos.
   * @return float La suma de los pagos.
   */
  public static function getTotalPayments($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE payment_status = 'Pagado' AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $total_payments = $stmt->fetchColumn();

    return $total_payments;
  }

  /**
   * Obtener los pagos no pagados
   *
   * Este método obtiene la suma de los pagos no pagados en los últimos 30 días.
   *
   * @param $db La conexión a la base de datos.
   * @return float La suma de los pagos no pagados.
   */
  public static function getUnpaidPayments($db)
  {
    $db = $db->getConnection();
    $stmt = $db->prepare("SELECT SUM(amount) FROM payments WHERE payment_status = 'Impagado' AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute();
    $unpaid_payments = $stmt->fetchColumn();

    return $unpaid_payments;
  }

  /**
   * Mandar recordatorio de pago al miembro por WhatsApp
   *
   * Este método envía un mensaje de recordatorio de pago al miembro a través de WhatsApp.
   *
   * @param $db La conexión a la base de datos.
   * @param int $member_id El id del miembro.
   * @param int $paymentId El id del pago.
   * @return bool Indica si el mensaje fue enviado correctamente.
   */
  public static function sendPaymentReminder($db, $member_id, $paymentId)
  {
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
