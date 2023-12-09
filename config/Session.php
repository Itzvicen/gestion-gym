<?php

class Session
{
  private static $instance;

  private function __construct()
  {
    session_start();
  }

  public static function getInstance()
  {
    if (self::$instance == null) {
      self::$instance = new Session();
    }

    return self::$instance;
  }

  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public function get($key)
  {
    return $_SESSION[$key] ?? null;
  }

  public function remove($key)
  {
    unset($_SESSION[$key]);
  }

  public function exists($key)
  {
    return isset($_SESSION[$key]);
  }

  public function destroy()
  {
    session_destroy();
  }
}