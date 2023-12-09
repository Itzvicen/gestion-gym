<?php
/**
 * Clase para generar y validar tokens CSRF.
 */
class CsrfToken
{
	private static $session;

	/**
	 * Genera un token CSRF y lo guarda en la sesión si no existe.
	 *
	 * @return string El token CSRF generado.
	 */
	public static function generateToken()
	{
		if (self::$session === null) {
			self::$session = Session::getInstance();
		}

		if (!self::$session->exists('csrf_token')) {
			self::$session->set('csrf_token', bin2hex(random_bytes(32)));
		}
		return self::$session->get('csrf_token');
	}

	/**
	 * Valida un token CSRF comparándolo con el token almacenado en la sesión.
	 *
	 * @param string $token El token CSRF a validar.
	 * @return bool True si el token es válido, False en caso contrario.
	 */
	public static function validateToken($token)
	{
		if (self::$session === null) {
			self::$session = Session::getInstance();
		}

		return (self::$session->exists('csrf_token') && hash_equals(self::$session->get('csrf_token'), $token));
	}
}