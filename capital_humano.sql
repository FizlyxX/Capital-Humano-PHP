-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 22, 2025 at 06:33 PM
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `colaboradores`
--

INSERT INTO `colaboradores` (`id_colaborador`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `sexo`, `identificacion`, `fecha_nacimiento`, `correo_personal`, `telefono`, `celular`, `direccion`, `ruta_foto_perfil`, `ruta_historial_academico_pdf`, `fecha_creacion`, `activo`) VALUES
(1, 'Juan', 'Carlos', 'Pérez', 'García', 'M', '8-765-4389', '1990-05-15', 'juan.perez@example.com', '222-3334', '6555-4444', 'Calle Principal, Edificio Central, Apt. 5, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_687fbd14495b6.jpeg', NULL, '2025-07-22 15:52:18', 1),
(2, 'Ana', 'María', 'González', 'Rojas', 'F', '9-876-5432', '1992-11-28', 'ana.gonzalez@example.com', '333-4444', '6777-8888', 'Avenida Central, Edificio Sol, Apt. 10, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_687fc5fa25155.jpeg', NULL, '2025-07-22 17:10:18', 0);

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
(3, 'Maria', '$2y$10$iIISztKChxQ5OQc6Srnut.jA4OAIKR20GMW40zWqm.PTTNJ2z6126', 2, 1),
(4, 'Rey', '$2y$10$jb9HZDGEyuf3dUslcbl25e3XCb6bmHwr8/s4B0tk6qRnqaL.XbvCq', 3, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
