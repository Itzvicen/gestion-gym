<?php
class LogoutController
{
  private $session;

  public function __construct()
  {
    $this->session = Session::getInstance();
  }

  public function logout()
  {
    $this->session->destroy();
    header('Location: /');
    exit;
  }
}