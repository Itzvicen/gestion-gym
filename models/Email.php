<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu estructura de directorios

class Email
{
  public static function sendEmail($member, $training)
  {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../config/');
    $dotenv->load();

    $mail = new PHPMailer(true);

    try {
      // Configuración del servidor
      $mail->isSMTP();
      $mail->Host = $_ENV['MAIL_HOST'];
      $mail->SMTPAuth = true;
      $mail->Username = $_ENV['MAIL_USERNAME'];
      $mail->Password = $_ENV['MAIL_PASSWORD'];
      $mail->SMTPSecure = 'tls';
      $mail->Port = $_ENV['MAIL_PORT'];

      // Tiempo de espera y depuración
      $mail->Timeout = 30;
      $mail->SMTPDebug = 2;

      // Destinatarios
      $mail->setFrom('info@gym.vicentesantiago.tech', 'Info');
      $mail->addAddress($member['email'], $member['first_name']);

      // Contenido
      $mail->isHTML(true);
      $mail->Subject = 'Inscripción a clase';
      $mail->Body    = 'Hola ' . $member['first_name'] . ', te has inscrito a la clase ' . $training['class_name'] . ' el día ' . $training['class_days'] . ' a las ' . $training['class_duration'] . ' en ' . $training['location'];

      $mail->send();
    } catch (Exception $e) {
      error_log('El mensaje no pudo ser enviado. Mailer Error: ' . $mail->ErrorInfo);
    }
  }
}
