-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 27-11-2023 a las 12:12:40
-- Versión del servidor: 10.9.3-MariaDB-1:10.9.3+maria~ubu2204
-- Versión de PHP: 8.0.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gym_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT 'uploads/pictures/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `members`
--

INSERT INTO `members` (`id`, `first_name`, `last_name`, `email`, `phone`, `birth_date`, `registration_date`, `active`, `image_path`) VALUES
(1, 'Perro', 'Pérez', 'juan.perez@example.com', '555-123-4567', '1980-05-01', '2022-01-01', 0, 'uploads/pictures/default.png'),
(2, 'Juan', 'García', 'ana.garcia@example.com', '555-123-4568', '1981-06-02', '2022-01-02', 1, 'uploads/pictures/default.png'),
(3, 'Pepe', 'Martínez', 'carlos.martinez@example.com', '555-123-4569', '1982-07-03', '2022-01-03', 1, 'uploads/pictures/default.png'),
(4, 'Isabel', 'Rodríguez', 'isabel.rodriguez@example.com', '555-123-4570', '1983-08-04', '2022-01-04', 1, 'uploads/pictures/default.png'),
(5, 'Alberto', 'González', 'fernando.gonzalez@example.com', '555-123-4571', '1984-09-05', '2022-01-05', 1, 'uploads/pictures/default.png'),
(6, 'Laura', 'Muñoz', 'laura.munoz@example.com', '555-123-4796', '2000-01-17', '2023-01-30', 1, 'uploads/pictures/default.png'),
(7, 'Cristina', 'Fernández', 'cristina.fernandez@example.com', '555-123-4572', '1985-10-06', '2022-01-06', 1, 'uploads/pictures/default.png'),
(8, 'Javier', 'López', 'javier.lopez@example.com', '555-123-4573', '1986-11-07', '2022-01-07', 1, 'uploads/pictures/default.png'),
(9, 'Sofía', 'Morales', 'sofia.morales@example.com', '555-123-4574', '1987-12-08', '2022-01-08', 1, 'uploads/pictures/default.png'),
(10, 'Sergio', 'Alvarez', 'sergio.alvarez@example.com', '555-123-4575', '1988-01-09', '2022-01-09', 1, 'uploads/pictures/default.png'),
(11, 'María', 'Guerrero', 'maria.guerrero@example.com', '555-123-4576', '1989-02-10', '2022-01-10', 1, 'uploads/pictures/default.png'),
(12, 'David', 'Ramírez', 'david.ramirez@example.com', '555-123-4577', '1990-03-11', '2022-01-11', 1, 'uploads/pictures/default.png'),
(13, 'Eva', 'Herrera', 'eva.herrera@example.com', '555-123-4578', '1991-04-12', '2022-01-12', 1, 'uploads/pictures/default.png'),
(14, 'Antonio', 'Navarro', 'antonio.navarro@example.com', '555-123-4579', '1992-05-13', '2022-01-13', 1, 'uploads/pictures/default.png'),
(15, 'Patricia', 'Ruiz', 'patricia.ruiz@example.com', '555-123-4580', '1993-06-14', '2022-01-14', 1, 'uploads/pictures/default.png'),
(16, 'Miguel', 'Mendoza', 'miguel.mendoza@example.com', '555-123-4581', '1994-07-15', '2022-01-15', 1, 'uploads/pictures/default.png'),
(17, 'Rosa', 'Peña', 'rosa.pena@example.com', '555-123-4582', '1995-08-16', '2022-01-16', 1, 'uploads/pictures/default.png'),
(18, 'Alejandro', 'Aguirre', 'alejandro.aguirre@example.com', '555-123-4583', '1996-09-17', '2022-01-17', 1, 'uploads/pictures/default.png'),
(19, 'Carmen', 'Reyes', 'carmen.reyes@example.com', '555-123-4584', '1997-10-18', '2022-01-18', 1, 'uploads/pictures/default.png'),
(20, 'José', 'Gutiérrez', 'jose.gutierrez@example.com', '555-123-4585', '1998-11-19', '2022-01-19', 1, 'uploads/pictures/default.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` enum('Pagado','Impagado') NOT NULL DEFAULT 'Impagado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `payments`
--

INSERT INTO `payments` (`id`, `member_id`, `amount`, `payment_date`, `payment_method`, `payment_status`) VALUES
(1, 1, '50.00', '2023-01-01', 'Efectivo', 'Pagado'),
(2, 1, '50.00', '2023-02-01', 'Efectivo', 'Pagado'),
(3, 2, '100.00', '2023-01-02', 'Tarjeta de crédito', 'Pagado'),
(4, 3, '75.00', '2023-01-03', 'Transferencia bancaria', 'Pagado'),
(5, 4, '200.00', '2023-02-04', 'PayPal', 'Pagado'),
(6, 5, '150.00', '2023-11-09', 'Efectivo', 'Impagado'),
(7, 7, '80.00', '2023-01-07', 'Efectivo', 'Pagado'),
(8, 7, '80.00', '2023-02-07', 'Efectivo', 'Pagado'),
(9, 8, '90.00', '2023-01-08', 'Tarjeta de crédito', 'Pagado'),
(10, 8, '90.00', '2023-02-08', 'Tarjeta de crédito', 'Impagado'),
(11, 9, '120.00', '2023-01-09', 'Transferencia bancaria', 'Pagado'),
(12, 10, '50.00', '2023-02-10', 'PayPal', 'Pagado'),
(13, 10, '50.00', '2023-03-10', 'PayPal', 'Impagado'),
(14, 11, '70.00', '2023-03-11', 'Efectivo', 'Pagado'),
(15, 12, '60.00', '2023-04-12', 'Efectivo', 'Pagado'),
(16, 13, '80.00', '2023-05-13', 'Tarjeta de crédito', 'Pagado'),
(17, 14, '110.00', '2023-06-14', 'Transferencia bancaria', 'Pagado'),
(18, 15, '40.00', '2023-07-15', 'PayPal', 'Impagado'),
(19, 16, '60.00', '2023-08-16', 'Efectivo', 'Pagado'),
(20, 17, '90.00', '2023-09-17', 'Efectivo', 'Pagado'),
(21, 18, '100.00', '2023-10-18', 'Tarjeta de crédito', 'Pagado'),
(22, 19, '120.00', '2023-11-19', 'Transferencia bancaria', 'Pagado'),
(23, 20, '50.00', '2023-12-20', 'PayPal', 'Impagado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `training_classes`
--

CREATE TABLE `training_classes` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `class_days` varchar(255) NOT NULL,
  `class_time` time NOT NULL,
  `class_duration` time NOT NULL,
  `instructor` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `training_classes`
--

INSERT INTO `training_classes` (`id`, `member_id`, `class_name`, `class_date`, `class_duration`, `instructor`, `location`) VALUES
(1, 1, 'Clase de Spinning', '2023-01-01', '10:00:00', 'Ana Gómez', 'Sala de Spinning'),
(2, 2, 'Yoga Matutino', '2023-01-02', '09:00:00', 'Carlos Sánchez', 'Sala de Yoga'),
(3, 3, 'Zumba Fitness', '2023-01-03', '11:00:00', 'Laura Rodríguez', 'Sala Principal'),
(4, 4, 'Entrenamiento Funcional', '2023-01-04', '16:00:00', 'David Pérez', 'Sala de Entrenamiento'),
(5, 5, 'Pilates para Principiantes', '2023-01-05', '17:30:00', 'Sofía Martínez', 'Sala de Pilates'),
(6, 6, 'Clase de Boxeo', '2023-01-06', '19:00:00', 'Javier García', 'Ring de Boxeo'),
(7, 7, 'Entrenamiento de Fuerza', '2023-01-07', '14:00:00', 'María López', 'Sala de Entrenamiento'),
(8, 8, 'Aeróbicos', '2023-01-08', '18:00:00', 'José Ramírez', 'Sala Principal'),
(9, 9, 'Yoga Vespertino', '2023-01-09', '20:00:00', 'Elena Fernández', 'Sala de Yoga'),
(10, 10, 'Clase de Baile', '2023-01-10', '19:30:00', 'Hugo Ruiz', 'Sala de Baile');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member_classes`
--

CREATE TABLE `member_classes` (
  `member_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  PRIMARY KEY (`member_id`, `class_id`),
  FOREIGN KEY (member_id) REFERENCES members(id),
  FOREIGN KEY (class_id) REFERENCES training_classes(id)
);
--
-- Volcado de datos para la tabla `member_classes`
--

INSERT INTO `member_classes` (`member_id`, `class_id`) VALUES
(6, 1),
(12, 2),
(29, 1),
(30, 3),
(31, 2),
(32, 4),
(33, 5),
(12, 6),
(6, 7)

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES
(1, 'administrador', '$2b$10$rYvny040QTHISV2TwKX50eQiu4o7nuCWJoMLhbI3XfyGlRTa2g0/W', 'admin@gym.es'),
(2, 'admin', '12345', 'admin@neatly.es');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
