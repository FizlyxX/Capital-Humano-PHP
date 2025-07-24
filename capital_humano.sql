-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 24, 2025 at 01:36 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capital_humano`
--

-- --------------------------------------------------------

--
-- Table structure for table `colaboradores`
--

DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE IF NOT EXISTS `colaboradores` (
  `id_colaborador` int NOT NULL AUTO_INCREMENT,
  `primer_nombre` varchar(100) NOT NULL,
  `segundo_nombre` varchar(100) DEFAULT NULL,
  `primer_apellido` varchar(100) NOT NULL,
  `segundo_apellido` varchar(100) DEFAULT NULL,
  `sexo` enum('M','F','Otro') NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo_personal` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `direccion` text,
  `ruta_foto_perfil` varchar(255) DEFAULT NULL,
  `ruta_historial_academico_pdf` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_colaborador`),
  UNIQUE KEY `identificacion` (`identificacion`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `colaboradores`
--

INSERT INTO `colaboradores` (`id_colaborador`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `sexo`, `identificacion`, `fecha_nacimiento`, `correo_personal`, `telefono`, `celular`, `direccion`, `ruta_foto_perfil`, `ruta_historial_academico_pdf`, `fecha_creacion`, `activo`) VALUES
(1, 'Juan', 'Carlos', 'Pérez', 'García', 'M', '8-765-4389', '1990-05-15', 'juan.perez@example.com', '222-3334', '6555-4444', 'Calle Principal, Edificio Central, Apt. 5, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_687fbd14495b6.jpeg', NULL, '2025-07-22 15:52:18', 1),
(2, 'Ana', 'María', 'González', 'Rojas', 'F', '9-876-5432', '1992-11-28', 'ana.gonzalez@example.com', '333-4444', '6777-8888', 'Avenida Central, Edificio Sol, Apt. 10, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_687fc5fa25155.jpeg', NULL, '2025-07-22 17:10:18', 1),
(3, 'Luis', 'Fernando', 'Castro', 'Castro', 'M', '9-876-543', '1985-07-20', 'luis.castro@gmail.com', '333-2542', '6777-2222', 'Avenida Central, Casa #10, David, Chiriquí', '../uploads/fotos_perfil/original_foto_688132d401cfd.jpeg', NULL, '2025-07-23 19:07:00', 1),
(4, 'Sofía', 'Isabel', 'Vargas', 'Torres', 'F', 'E-5-98765', '1993-01-11', 'sofia.vargas@outlook.com', '444-3333', '6888-3333', 'Vía España, PH Océano, Piso 12, Apt. 12B, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_6881337772aa2.jpeg', NULL, '2025-07-23 19:09:43', 1),
(5, 'Ricardo', 'Javier', 'Núñez', 'Blanco', 'M', '7-654-3210', '1988-01-25', 'ricardo.nunez@example.com', '555-4444', '6999-4444', 'Calle 50, Plaza Corporativa, Oficina 701, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_688134a84b344.jpeg', NULL, '2025-07-23 19:14:48', 1),
(6, 'Pedro', 'José', 'Martínez', 'López', 'M', '4-567-890', '1995-02-28', 'pedro.martinez@gmail.com', '789-0123', '6123-4567', 'Calle La Amistad, Edificio Sol, Apt. 5B, Santiago, Veraguas', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:21:53', 0),
(7, 'María', 'Cristina', 'Ramírez', 'Díaz', 'F', '1-234-567', '1989-09-10', 'maria.ramirez@example.com', '123-4567', '6987-6543', 'Avenida del Mar, PH Marina, Piso 8, Apt. 8A, Colón', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 1),
(8, 'Carlos', 'Alberto', 'Gómez', 'Silva', 'M', '3-456-789', '1991-04-03', 'carlos.gomez@example.com', '456-7890', '6543-2109', 'Vía Porras, Centro Comercial, Oficina 203, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 1),
(9, 'Laura', 'Cecilia', 'Fernández', 'Ruiz', 'F', 'PE-10-123', '1994-06-18', 'laura.fernandez@example.com', '789-1234', '6109-8765', 'Barriada La Esperanza, Casa F-1, La Chorrera', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 0),
(10, 'Jorge', 'Andrés', 'Díaz', 'Morales', 'M', 'N-15-987', '1987-12-05', 'jorge.diaz@example.com', '210-9876', '6321-0987', 'Corregimiento de Bella Vista, Calle 42, Edificio XYZ, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 0),
(11, 'Carolina', 'Estefanía', 'Herrera', 'Vásquez', 'F', '8-901-234', '1996-08-22', 'carolina.herrera@example.com', '345-6789', '6567-8901', 'Cerro Silvestre, Calle Los Lirios, Casa 5, Arraiján', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 1),
(12, 'Miguel', 'Ángel', 'Castro', 'Pinto', 'M', 'CH-20-567', '1990-01-11', 'miguel.castro@example.com', '678-9012', '6789-0123', 'Vía Interamericana, Sector 1, Casa 15, Penonomé', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso completo al sistema y gestión de usuarios.'),
(2, 'RRHH', 'Gestión de colaboradores, cargos y reportes.'),
(3, 'Empleado', 'Acceso a su perfil y funcionalidades de autoservicio.');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `id_rol` int DEFAULT '3',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  KEY `fk_id_rol` (`id_rol`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `contrasena`, `id_rol`, `activo`) VALUES
(1, 'admin', '$2y$10$S6hryAResvlDYo0n7W7OaOw2kFuRpsMfd1nJNaZQgLSl4qkEMQWuu', 1, 1),
(2, 'Nate', '$2y$10$Mb3iA7gICkTjD7lKhSmn9OQs32TtZlhdNVhvfkadgaJZ8vk1go/la', 2, 1),
(3, 'Maria', '$2y$10$iIISztKChxQ5OQc6Srnut.jA4OAIKR20GMW40zWqm.PTTNJ2z6126', 2, 0),
(4, 'Rey', '$2y$10$jb9HZDGEyuf3dUslcbl25e3XCb6bmHwr8/s4B0tk6qRnqaL.XbvCq', 3, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
