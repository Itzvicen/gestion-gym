<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Ajusta la ruta según tu estructura de directorios

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views/templates');
$twig = new \Twig\Environment($loader);

// Devuelve la instancia de Twig
return $twig;
