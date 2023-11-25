<?php 

class LogoutController {
  public function logout() {
      session_destroy();
      header('Location: /');
      exit;
  }
}
