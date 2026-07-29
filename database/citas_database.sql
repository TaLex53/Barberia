-- --------------------------------------------------------
-- Tablas para el Módulo de Citas y Clientes
-- --------------------------------------------------------

-- Tabla de Horarios (Pre-poblada según especificaciones)
CREATE TABLE IF NOT EXISTS `horarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hora` time NOT NULL,
  `turno` enum('Mañana','Tarde','Noche') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `horarios` (`hora`, `turno`) VALUES
('09:00:00', 'Mañana'),
('09:45:00', 'Mañana'),
('10:30:00', 'Mañana'),
('11:15:00', 'Mañana'),
('12:00:00', 'Tarde'),
('12:45:00', 'Tarde'),
('13:30:00', 'Tarde'),
('14:15:00', 'Tarde'),
('15:00:00', 'Tarde'),
('15:45:00', 'Tarde'),
('16:30:00', 'Tarde'),
('17:15:00', 'Tarde'),
('18:00:00', 'Noche'),
('18:45:00', 'Noche'),
('19:20:00', 'Noche'),
('20:00:00', 'Noche');

-- Tabla de Clientes (Basado en el formulario requerido)
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de Servicios (Para vincular citas reales)
CREATE TABLE IF NOT EXISTS `servicios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL DEFAULT 'Servicios Esenciales',
  `duracion_minutos` int(11) NOT NULL DEFAULT 45,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `servicios` (`nombre`, `duracion_minutos`, `precio`) VALUES
('Corte Degradado', 40, 12000),
('Barba Simple', 20, 7000),
('Barba con Vapor', 40, 10000),
('Color Global', 240, 60000);

-- Tabla de Citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `barbero_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `fecha_cita` date NOT NULL,
  `horario_id` int(11) NOT NULL,
  `estado` enum('Agendada','Completada','Cancelada') NOT NULL DEFAULT 'Agendada',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`barbero_id`) REFERENCES `barberos`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`horario_id`) REFERENCES `horarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
