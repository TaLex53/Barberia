-- --------------------------------------------------------
-- Base de datos: `cutlevel_barber`
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_details`
--
CREATE TABLE IF NOT EXISTS `user_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `user_details`
-- Contraseña encriptada para 'admin' (generada con password_hash de PHP)
--
INSERT INTO `user_details` (`id`, `username`, `password`) VALUES
(1, 'cutz', '$2y$10$ImE/ir8oke9yO4big/u5w.oli3HyJKBquGejDU5lhIWCq7mhfEUEG'); 
-- NOTA: El hash de arriba corresponde a la contraseña "level2026%!". 
-- Por ahora, usarás usuario "cutz" y contraseña "level2026%!".

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barberos`
--
CREATE TABLE IF NOT EXISTS `barberos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos inicial para la tabla `barberos`
--
INSERT INTO `barberos` (`nombre`, `apellido`, `activo`) VALUES
('Nicolás', 'Cerda', 1),
('Jorge', 'Valenzuela', 1),
('Alexandra', 'Orellana', 1);
