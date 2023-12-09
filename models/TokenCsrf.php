<?php

/**
 * Clase para generar y validar tokens CSRF.
 */
class CsrfToken
{
	
	/**
	 * Genera un token CSRF y lo guarda en la sesión si no existe.
	 *
	 * @return string El token CSRF generado.
	 */
	public static function generateToken()
	{
		if (!isset($_SESSION['csrf_token'])) {
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf_token'];
	}

	/**
	 * Valida un token CSRF comparándolo con el token almacenado en la sesión.
	 *
	 * @param string $token El token CSRF a validar.
	 * @return bool True si el token es válido, False en caso contrario.
	 */
	public static function validateToken($token)
	{
		return (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token));
	}
}
