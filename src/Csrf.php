<?php
// src/Csrf.php
class Csrf {
  public static function token(): string {
    Session::start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
  }

  public static function check(?string $token): bool {
    Session::start();
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
  }
}
