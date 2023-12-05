<?php

class WhatsAppSender
{
  private $url;
  private $token;

  public function __construct($url, $token)
  {
    $this->url = $url;
    $this->token = $token;
  }

  // Funcion para enviar mensaje de bienvenida a un nuevo miembro
  public function sendAddedMessage($to, $firstName, $lastName, $className, $classDays)
  {
    $messageBody = "Hola, {$firstName} {$lastName}👋🏻\n\nHas sido añadido a la clase {$className} los días {$classDays}.\n\n¡Nos vemos en el entrenamiento!";
    return $this->sendMessage($to, $messageBody);
  }

  // Funcion para enviar mensaje de eliminacion de un miembro de una clase
  public function sendRemovedMessage($to, $lastName, $firstName)
  {
    $messageBody = "Hola, {$firstName} {$lastName}👋🏻\n\nLamentamos informarte que has sido eliminado de la clase.\n\nSi esto es un error o tienes preguntas, por favor contacta con nosotros.";
    return $this->sendMessage($to, $messageBody);
  }

  // Funcion para enviar mensaje de recordario de un pago a un miembro
  public function sendPaymentReminder($to, $firstName, $lastName, $paymentAmount)
  {
    $messageBody = "Hola, {$firstName} {$lastName}👋🏻\n\nTe recordamos que tienes que pagar {$paymentAmount}€ de tu cuota mensual.\n\nSi ya has realizado el pago, por favor ignora este mensaje.";
    return $this->sendMessage($to, $messageBody);
  }

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
