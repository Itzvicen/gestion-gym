<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu estructura de directorios

class Email
{
  public static function sendEmail($member, $training)
  {
    $mail = new PHPMailer(true);

    try {
      // Configuración del servidor
      $mail->isSMTP();
      $mail->Host = 'gym.vicentesantiago.tech';
      $mail->SMTPAuth = true;
      $mail->Username = 'info@gym.vicentesantiago.tech';
      $mail->Password = 'EWp9:!d6f2FNP!f';
      $mail->SMTPSecure = 'ssl';
      $mail->Port = 465;

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
