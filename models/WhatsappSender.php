<?php
/**
 * Clase WhatsAppSender
 *
 * Esta clase se utiliza para enviar mensajes de WhatsApp a los miembros de una clase de gimnasio.
 */
class WhatsAppSender
{
  private $url;
  private $token;

  /**
   * Constructor de la clase WhatsAppSender
   *
   * @param string $url La URL de la API de WhatsApp
   * @param string $token El token de autenticación para la API de WhatsApp
   */
  public function __construct($url, $token)
  {
    $this->url = $url;
    $this->token = $token;
  }

  /**
   * Enviar mensaje de bienvenida a un nuevo miembro
   *
   * @param string $to El número de teléfono del destinatario
   * @param string $firstName El nombre del nuevo miembro
   * @param string $lastName El apellido del nuevo miembro
   * @param string $className El nombre de la clase a la que se ha añadido el nuevo miembro
   * @param string $classDays Los días en los que se lleva a cabo la clase
   * @return bool True si el mensaje se envió correctamente, False en caso contrario
   */
  public function sendAddedMessage($to, $firstName, $lastName, $className, $classDays)
  {
    $messageBody = "Hola, {$firstName} {$lastName}👋🏻\n\nHas sido añadido a la clase {$className} los días {$classDays}.\n\n¡Nos vemos en el entrenamiento!";
    return $this->sendMessage($to, $messageBody);
  }

  /**
   * Enviar mensaje de eliminación de un miembro de una clase
   *
   * @param string $to El número de teléfono del destinatario
   * @param string $lastName El apellido del miembro eliminado
   * @param string $firstName El nombre del miembro eliminado
   * @return bool True si el mensaje se envió correctamente, False en caso contrario
   */
  public function sendRemovedMessage($to, $lastName, $firstName)
  {
    $messageBody = "Hola, {$firstName} {$lastName}👋🏻\n\nLamentamos informarte que has sido eliminado de la clase.\n\nSi esto es un error o tienes preguntas, por favor contacta con nosotros.";
    return $this->sendMessage($to, $messageBody);
  }

  /**
   * Enviar recordatorio de pago a un miembro
   *
   * @param string $to El número de teléfono del destinatario
   * @param string $firstName El nombre del miembro
   * @param string $lastName El apellido del miembro
   * @param float $paymentAmount El monto del pago pendiente
   * @return bool True si el mensaje se envió correctamente, False en caso contrario
   */
  public function sendPaymentReminder($to, $firstName, $lastName, $paymentAmount)
  {
    $messageBody = "Hola, {$firstName} {$lastName}👋🏻\n\nTe recordamos que tienes que pagar {$paymentAmount}€ de tu cuota mensual.\n\nSi ya has realizado el pago, por favor ignora este mensaje.";
    return $this->sendMessage($to, $messageBody);
  }

  /**
   * Enviar un mensaje de WhatsApp
   *
   * @param string $to El número de teléfono del destinatario
   * @param string $messageBody El contenido del mensaje
   * @return bool True si el mensaje se envió correctamente, False en caso contrario
   */
  private function sendMessage($to, $messageBody)
  {
    $whatsappMessage = [
      "messaging_product" => "whatsapp",
      "to" => $to,
      "type" => "text",
      "text" => [
        "body" => $messageBody
      ]
    ];

    $ch = curl_init($this->url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($whatsappMessage));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      "Authorization: Bearer {$this->token}"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Comprobar si la solicitud fue exitosa
    if ($httpcode == 200) {
      return true;
    } else {
      return false;
    }
  }
}
