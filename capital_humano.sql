-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 24, 2025 at 10:38 PM
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
-- Table structure for table `cargos`
--

DROP TABLE IF EXISTS `cargos`;
CREATE TABLE IF NOT EXISTS `cargos` (
  `id_cargo` int NOT NULL AUTO_INCREMENT,
  `id_colaborador` int NOT NULL,
  `id_departamento` int NOT NULL,
  `id_ocupacion` int NOT NULL,
  `sueldo` decimal(10,2) NOT NULL,
  `fecha_contratacion` date NOT NULL,
  `tipo_colaborador` enum('Permanente','Eventual','Interino') NOT NULL,
  `activo_en_cargo` tinyint(1) DEFAULT '1',
  `firma_datos` text,
  `fecha_firma` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cargo`),
  KEY `id_colaborador` (`id_colaborador`),
  KEY `id_departamento` (`id_departamento`),
  KEY `id_ocupacion` (`id_ocupacion`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4

--
-- Dumping data for table `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `id_colaborador`, `id_departamento`, `id_ocupacion`, `sueldo`, `fecha_contratacion`, `tipo_colaborador`, `activo_en_cargo`, `firma_datos`, `fecha_firma`) VALUES
(1, 1, 3, 1, 2100.00, '2020-01-10', 'Permanente', 0, 'LXT3bHjY9dWE5urxK1GGnE5vRttmMi1OvVcm9X10TLT8K3aX9Sb9Fo+NK4Sr22tq5YtIJRO64N4ZxMjphvNSLz1GToZQlvWMvYUEotdqYy2sjmlN2OoK23IHuL/Pua1+eJST0it2q6vA7T95tGWKjqE30yXZDFCsqIwDwvZao3PQgacAtnnXQDrlAkIu4HB0uglnW5Wddx1+p4mg66+rU6wWJNkUO2AHaiBLjTo8BzdKCW+vfOhy0S5gNeZDQeJQsf71etLdtgU9SYcEyZkniMwu+rIvay7k8o5GvsGn68VN6FvQqfd5ChkwF8JIkxMxJ1/2Lf5cg9bKjn8D64XXIQ==', '2025-07-24 09:27:58'),
(2, 2, 1, 4, 1750.00, '2021-03-15', 'Permanente', 0, 'oQbt1BtZCzqsA+EWbfa/toP+3XBtGjXt9soiTe6TL7M8Ijl644EMgy0zbgWG97PzobCA1iHkukiOxe0VTXQrLLNDDwMD0ykzwYj1WJNe5Aayvx8e0ZKZjgfBMOe1zur1u+5wdZxMgYtXlxlQwgoUNfj/VRk0p6JOgh6iic+dIoeblpFKf+LYL/KJsA88a20yhtasocGNiQfXzWcvdYURlIecOyk2acyuiOPSRtP4V6ftg5k0PnDrrlJCB53NSMedce+yAVefazEknTRjBujsLGQ8xm2pSzG/+V34IY14j2wLgpMofy4z8c7oBqEiZf6NCDuY2r6ERcprTnVucmDCwQ==', '2025-07-24 09:36:05'),
(3, 4, 2, 3, 700.00, '2025-02-02', 'Interino', 1, 'dnbyBWdwoO/HidpOH25KMFSf2boqimD4HuIryIuajNevGiSDuI3cNTjsvQjZfP9qL0lxcJ2AhmjxTVuV3O952FmuHBfylGbGj2AN1+NMXZNri9tTi63hFaSCH8BaWPqtnhN6Q7i/PoLv6+SjGpJMj7E9CpJj2JNsmxVLa4iymj0GETPhjjmLzIjuQH8MRphIM8cK9NfOSmrv+P0jkhLd+J2qaH3jP4Dvy9aGc6R4pOX+zU/VR2oWzpBRqlqWF1vVIVJhX+Y0nxFj6MVDOjYhA6GKKjQz1xzplMsYmLXiKQQYiPQKXQGi1rF1O9xYaul6j6oY31x7VaO9+mWTXVsrhA==', '2025-07-24 22:32:46'),
(4, 5, 4, 2, 900.00, '2022-01-10', 'Eventual', 1, 'iwajXOy7EJqClio4AMk0CkC5IBlyS29WC2Xg2rDn3KQ3U7zPTynCnYl+6O4mRwwnHkfcAFc5cHNmOV6ALdzV8UjwCozjfFPzyJOguuV+ZNQJ4wBtOhpsdlz/Wh0yZrPWePqXfFN3nyujh3UrBCHpiqkYFL+zDLjSqIlLucnBk2pdMBALW6B48k0Szl8Goin4c83CcxV9OgvYuqW/FPkgMO/vaNDUeauqSaKbNgJtzLV1o4o5VtQJdL3Aj0wJP5LItAlSEa/B4MUPWiOz403JcYsQtSfgHoktqpafIQ1FkD8ywnxVHEnqqABPoRaS0tk2g1P/n+oUwY7YwwvI3UDHCA==', '2025-07-24 22:32:53'),
(5, 9, 1, 4, 1800.00, '2018-12-05', 'Permanente', 1, 'SW1SC0xMW/P3QwWDiNTyBaLGX9lzYyVkL2KFb8mThaKTGTnSFCuTo7jm5dTyvZFmqpR6zpTbIzdmLk4Cnc60IIlHfaexF4DSm09cq69EriOnXCI+zgpViYZWzkeJXcqUJ/kD16GI7J/T2sr370ToUfw5ldBzi7XXVVlG37XtZyUdU9lcdeFh4KiZMUCEz68qOIP1fR/u1edGYHIrta8w74dp6+FFqubSC+jAsKR0HrE2YiwsZD/YaoHLEnb/CVnqhkRvRv34Q42WvH2wFZ4AV+11UVgpGl3wbwTqZ0GnlcfdiXdBaKzdapLx8gcPWU6HONHyW2cMivvfbtX7LmK0sg==', '2025-07-24 09:42:50'),
(6, 1, 3, 5, 2800.00, '2022-04-14', 'Permanente', 1, 'qWZfBpNJGorLtpDxDynp8IZkPqsm0VkFoAvMr3f8mG05yGCQ2O2mCUtXhhaL+or5J0n7W7cC4xNCZAzfpLsFS6KETae4y2y1Ki+rE6rBc4SYR1FKOJlBlP4qriKcIcdCorg7e5vkZxUsMTBNFeeQn4ZkJqdnOFDubYbbF2NjwhdoL36z/Um3LoPZWzl7yr9jctKgv+HRchXrnzYPyB3kmyFV1AwK6z+LKWYckQ9qpiOqJ9mQvZe84oOqsTqQc8PvXYw1CHx9IDOaHEbWvxNqb5RDIwzU0/OL3hpzFcyIsJewcl0k8Mni0AzjXmIpSgCu7VBuqMxadSMDogmxVRABFQ==', '2025-07-24 09:50:11'),
(7, 2, 1, 11, 2240.00, '2022-05-26', 'Permanente', 0, 'WIToHOMbVJjfykVMwuJyfXxFRfwYpN/jiS3HtaTpijjWQFTnEr+0tE2/9/4lSIWIN7e2BLsLMU7RDTNBDrN1j4KzLmzFUul5a5i28+qPAJ9DsnzhMFnFOaOTU5vX9mRV5jMHQgR4Z6NZED1YnNojieSgwQxrPCETRwp5SerSmxBpKEAigxGRwQBrNupLiyLDxwM2ohrRGRBcArtg9NT0Adx7sKrcW6EWHjg6EaB5hmrDhkQ7PxUXc9xtZhdHoUSuLiZpmtsOHnxJyWByAdJlte9lBthxuK+tMoYfrEWgsHlMN7BFFEzmHrssbTlearKC27Kd96rbFUzZ5nVokuBRUQ==', '2025-07-24 09:59:38'),
(8, 2, 1, 7, 3000.00, '2024-08-21', 'Permanente', 1, 'qHfVSrZDzyjRP/JLhxAqnjxIRhjvqZQaSexRjlhtaJQIQtusO9ADEQYwxb65Ekho+okHhj7x2Ut5JBmHI3Wtwlm1xzkgIpT1JXKMq3CUZhC+4AMU7aqIIOc2qNeMxY9Nqy9Tkzg9p8BBiV6sMWbRIsjohvY+tb34ST8E/JTFyo9F/ylEOfxlVhayQd0JHR7AdFWsZ17fRN8qzGD7+veGL4Z60YGmc81KMQARQGOgW3uxqlzXP2rL/LAfBbIRQ6yQYavtMYKqroRWUThqm8LPN1wVmT8UXD3UnuZZdvE2bn9cw6n76EMeeq+HP82xP2IUOQxfYVsJlU52f/bpdz+jTw==', '2025-07-24 10:00:55'),
(9, 6, 4, 12, 2200.00, '2020-05-01', 'Permanente', 1, 'K+qFtYeXhtDlRJWiE8atmcsVHfIVKXMHEQQZc3GApKFZPzHa1NpjnA/+6GVDrzlhRX3IEjs4ZedPvC3RK8KulbdjyFY0ULlt01A41OMk1YqlWNUE5ZOuQMPULmF4p5LFVI6F0g+Be1QKSls38fGeW+AH0tfubvjNWNjjLW8YORcfpXH+BczKFIVDoB2P6qR8w729EfbyDl1gjS0sF6oYSNycxdmQLiC2lVvN0El6SHSjpoM+1QiNiPtycieW0ElhqwkGcnycI/TTbdF5l5xdT/H3x3YlwcHAtmXVXjC0XYF0ufwkRU8iAW13tXqlrurzT0WrSF8p3nA7ThlRVV42cA==', '2025-07-24 20:27:56'),
(10, 7, 3, 1, 1850.00, '2020-07-15', 'Permanente', 0, 'CDQxqG61EjzITRPvE63dVOro6hDzP2+dEVPAOWyw9niVe9xgYVgHyUGqboWcVJyo/2qIEkxWiz/EM+430Nb/UQk9kDw4sRyETGwj+tQuMJWj88+Xvt8KgnwV7bQn5AYrA1vz70ZjUE6848rxnBKZM3h+tcgCR8ubs/5Jp7PLxdqu4sH+7DgKWk8SSutVnGQvcOfggCmQfjiIq40RTKWzXJJMRFr4LC9w1DX0XhKlYnDnR+Lh3KkBAVtlVz93ekJJXEw49Weqo2bAgX6BxGykgZuK+OZqHLA+CTJ+hyt6LcwBesF1iDiwJGnn/dCV6nXeLf4P+7y6Rrr/Y+DM94bknA==', '2025-07-24 19:47:13'),
(11, 7, 3, 10, 2500.00, '2023-02-12', 'Permanente', 1, 'ihT1yH30v+FoH18OzDOEbBAg02165OaEKmJ+fBJGILMbXBbHnGb1j1yEs8w4jQjXJ2mgahZKR5btnsQgxVtU6A8j8BTa9RBoAJSkliOfMh4KnTTJbbFwSjCRZ6mhFXqv2OchplrZGqJVqZNlGIyhYpzWJkqTygCjdKuhoPUion2K4aPjFriXoqppuMNxq+1BJRcaaqLFT4pysHtMXZv4OmvgElKA4vnJB2X9hq5MPrb5TsUIolinKcoZezirjTk/0wjHUcdYMV8W+AWNVqLF+i852ZjG7/LVesttVVlNQu1+/oVX/NBkpTuOX5nb6xJBKm9+WKM7zC0KhypWvcH/rA==', '2025-07-24 19:48:22'),
(12, 10, 1, 11, 2100.00, '2021-09-20', 'Permanente', 1, 'S0QjIZ4NO57WUMIUeVLpBX39eLGtzQng02M3agmv1yDkz+mqqsEkvfMjCm6pcMe45P5y1DT0rWOAperdXOgadDOtO6DWPnS8NEOtlEcmgeLQvGtwb3I++byiJBiGEWLBXKv/THELhCUqrNINNbS5mZu3+TcJbvdtN4tG4D6KpgT/rPuCk4yxhkgGjkhRMT3sxLYekv0vVa8nbKhdNwECrdQtszgnH7u+URk2qolynfvk7o5zSlz6uZBjnh4oGBAU1CgrNJ+3q4nCJnPAoizedsB+EqiyzfL7Te7W7YgvM9qEGUAWFmF+13DiAsdvci/e8+ZgLbVGMrvM/gc7BXWqMA==', '2025-07-24 19:49:53'),
(13, 12, 4, 9, 2500.00, '2020-03-17', 'Permanente', 1, 'FtjW8Pvfsd023zE2D+FlvPzoLT9vEh1Ya00rjmeT9fO5msosEpurmw6Ls6Fq0HQCJouVpYgxGpGzUXLLmDs20vHrOXEeUmrTER/RkTiiwtnrxZgnTip8jjDwXPlIPbRCP8UARJUA7vPm1s0mwHcpItH+el+wUhABAohPtms97GY9p0R++nbEHnazzGqma6Hzeyv84VFeaApRg5tPUN3sEJZYp/q9ntuPZnR6r7rNTIri1+i3fhKFSV8L0woX+5jGwVfZM1ziTSqAeuaGUCP1dtQXVk1EUF5wewjFdE7de+rJEZZ+Jkr9SxQxst+zb9A+Tm+jnAkj3+0GgqLTBvQ32w==', '2025-07-24 20:27:40'),
(14, 3, 2, 8, 2600.00, '2019-06-01', 'Permanente', 1, 'a0v/KktydDscDbtvGmCImwHtKKM13vGh1XRGeMn0BuP+lLqdlTchqj/iURopx+TsSCHkwpgYXsY1riv/druj+e6RhNVUJIV1A9wqjrOA0qVl/bGMpAe+4OF+7YvOXpvLLNN6KjUK0UaKjn+3lyAyDqGCE4C6L9do2BxAynnYneEf8r8e+XBhLKy1R62ZschBdXHkfutxMrYPhrcv6q1/0mDkJ/CN5NRIuN6ozHbbcq6hiBLW2otd3VMuqgGA+KBp/yO3XpAWSX7IMNqxx3RugYklF5l2PnSvc352aflSWErJg2ATfH3b4AOcrBHgncqxtPNYfPYqgzdn/cNDvraK5Q==', '2025-07-24 20:45:21'),
(15, 8, 1, 4, 1300.00, '2023-08-01', 'Permanente', 0, 'UYDJqlMLYQosOerQsMVirpMJjQzaXjT64oCK4Ban5esFFVkwIysWRGv889Vqt9D6Zlr33jdN1TgIdbIn1NCXqoep5BkRWiQLzSueh0sDKRRb6CNncVhZ7Cbkqh91IqrG8xA6nlvJthd/MHbjZVaNOGPi9MKcMeR4NJfYnA4igq2iM1bdxeumxLajRb2qko6xJkmvD6w4HjG6EoV1at5KSxvL5v8X2G2b7qS3UFF4AQAzCiSy8jIEg8t+t3kv6kWYL8aCRaQ1xc9zuKmHteHNRdLFYTzdCcp+NxpTewnNyL0OVwQUCk8O+lIFhKulzfXdj0w5QFPFkUxcZP23YZheFQ==', '2025-07-24 20:47:14'),
(16, 8, 1, 12, 2000.00, '2025-07-01', 'Permanente', 1, 'E7lV6bMPlPWEfggQCQps3tAiln/PD4c46Wn3f2aZy1Y8fUCeEJUQCFAP1/fDoGxr6If1RMvI1ksC0V4QveTMqQgs6WHnKRuopZ72OPnjuAZvwudtMPqnGM63H7b0HUrKYgMCl6Xj5J+vphknOygnkWuxHPt1haF7k/Avdh0iOW2dMpo5FeeSMDUi9UtNmHNhvAP6dcTrUa7HYpbVoC+fhKWa3eTKLWtgdUrELANkzifkyjQi3skLV1yP9iLYhEN9q4fekTbRR98lFlSoCz6uO067FM1lxftK+Hda2PvfGEHhs2enkQUN3ICr4KSMdTfPg3QEFQ0ddbMxNtFbkW8YRQ==', '2025-07-24 22:26:09'),
(17, 11, 4, 5, 1800.00, '2022-10-10', 'Permanente', 0, 'DVBHGDlX3U3pKtfdU0He2txr1//psVJ0rmEq0L4Wn33KwaeYbbHPkTz5unH+1oeF81kuBxQAlNOusXPfC3vsWZx25FXU9WF+s1f7Px5aHZu7Jgt6pXjq+Uhaerl0V0HUke69bTd3dZ+iS2A4qGztAfajKP/wcVdpd8QSlORUpoMjXkMdjyWXwKojy7cCzlsfNeLD/VGDBhoLg3zr+1KWDKcAAlX198EuP3XtJaMEKDgdCbAOKnwi2gzb0WHdGS6BODhF2N4sRmF8OwCfIWyndyHyWowSatd/uYSIx9lVFFVA19ijM5iH4WcvxrSxWt2NhnXKS+slUTQLngcbi6NPtQ==', '2025-07-24 20:49:44'),
(18, 11, 4, 9, 2480.00, '2024-09-12', 'Permanente', 1, 'QOUVV6nZebVqdvYUpjHmB6Mc2VTdtFYQlyU+MItu62Z0tcBrZ9yjZ0Ht6v++Pps1+Dh/yOuZogeLEeQKEIe5sa2r3bg3cDUejFMfiVzyksU6lc2NEOA+RGJ7aD6X9CE3yxxNpMq4yM/TQfPM9p24D5I3vN82P8B37bEhBGJCzxfgXOnMavvDQ9cahQgI0NL2sBQFbHp+DF+xXhfFmiBHxysX3BMZD7oVZIeEzMThHmLH3Xc3j6SvxLnXAPnNeT6eIaXC+FQQZNBwxMNymG5orxnS14uNTrKSh3i+94Hv7IVohStFGvc/7vfyh5CsP4veA7V2i6M7HHH/xuZvCihkOQ==', '2025-07-24 22:32:14');

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
  `estatus_id` int NOT NULL DEFAULT '4',
  `fecha_ingreso` date NOT NULL,
  `fecha_salida` date DEFAULT NULL,
  `id_usuario` INT NULL,
  PRIMARY KEY (`id_colaborador`),
  UNIQUE KEY `identificacion` (`identificacion`),
  KEY `colaborador_fk` (`estatus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4

--
-- Dumping data for table `colaboradores`
--

INSERT INTO `colaboradores` (`id_colaborador`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `sexo`, `identificacion`, `fecha_nacimiento`, `correo_personal`, `telefono`, `celular`, `direccion`, `ruta_foto_perfil`, `ruta_historial_academico_pdf`, `fecha_creacion`, `activo`, `estatus_id`, `fecha_ingreso`, `fecha_salida`) VALUES
(1, 'Juan', 'Carlos', 'Pérez', 'García', 'M', '8-765-4389', '1990-05-15', 'juan.perez@example.com', '222-3334', '6555-4444', 'Calle Principal, Edificio Central, Apt. 5, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_687fbd14495b6.jpeg', NULL, '2025-07-22 15:52:18', 1, 4, '2020-01-10', NULL),
(2, 'Ana', 'María', 'González', 'Rojas', 'F', '9-876-5432', '1992-11-28', 'ana.gonzalez@example.com', '333-4444', '6777-8888', 'Avenida Central, Edificio Sol, Apt. 10, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_687fc5fa25155.jpeg', NULL, '2025-07-22 17:10:18', 1, 4, '2021-03-15', NULL),
(3, 'Luis', 'Fernando', 'Castro', 'Castro', 'M', '9-876-543', '1985-07-20', 'luis.castro@gmail.com', '333-2542', '6777-2222', 'Avenida Central, Casa #10, David, Chiriquí', '../uploads/fotos_perfil/original_foto_688132d401cfd.jpeg', NULL, '2025-07-23 19:07:00', 1, 4, '2019-06-01', NULL),
(4, 'Sofía', 'Isabel', 'Vargas', 'Torres', 'F', 'E-5-98765', '1993-01-11', 'sofia.vargas@outlook.com', '444-3333', '6888-3333', 'Vía España, PH Océano, Piso 12, Apt. 12B, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_6881337772aa2.jpeg', NULL, '2025-07-23 19:09:43', 1, 4, '2025-02-20', NULL),
(5, 'Ricardo', 'Javier', 'Núñez', 'Blanco', 'M', '7-654-3210', '1988-01-25', 'ricardo.nunez@example.com', '555-4444', '6999-4444', 'Calle 50, Plaza Corporativa, Oficina 701, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_688134a84b344.jpeg', NULL, '2025-07-23 19:14:48', 1, 4, '2022-01-10', NULL),
(6, 'Pedro', 'José', 'Martínez', 'López', 'M', '4-567-890', '1995-02-28', 'pedro.martinez@gmail.com', '789-0123', '6123-4567', 'Calle La Amistad, Edificio Sol, Apt. 5B, Santiago, Veraguas', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:21:53', 0, 4, '2020-05-01', NULL),
(7, 'María', 'Cristina', 'Ramírez', 'Díaz', 'F', '1-234-567', '1989-09-10', 'maria.ramirez@example.com', '123-4567', '6987-6543', 'Avenida del Mar, PH Marina, Piso 8, Apt. 8A, Colón', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 1, 4, '2020-07-15', NULL),
(8, 'Carlos', 'Alberto', 'Gómez', 'Silva', 'M', '3-456-789', '1991-04-03', 'carlos.gomez@example.com', '456-7890', '6543-2109', 'Vía Porras, Centro Comercial, Oficina 203, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 1, 4, '2023-08-01', NULL),
(9, 'Laura', 'Cecilia', 'Fernández', 'Ruiz', 'F', 'PE-10-123', '1994-06-18', 'laura.fernandez@example.com', '789-1234', '6109-8765', 'Barriada La Esperanza, Casa F-1, La Chorrera', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 0, 4, '2018-12-05', NULL),
(10, 'Jorge', 'Andrés', 'Díaz', 'Morales', 'M', 'N-15-987', '1987-12-05', 'jorge.diaz@example.com', '210-9876', '6321-0987', 'Corregimiento de Bella Vista, Calle 42, Edificio XYZ, Ciudad de Panamá', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 0, 4, '2021-09-20', NULL),
(11, 'Carolina', 'Estefanía', 'Herrera', 'Vásquez', 'F', '8-901-234', '1996-08-22', 'carolina.herrera@example.com', '345-6789', '6567-8901', 'Cerro Silvestre, Calle Los Lirios, Casa 5, Arraiján', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 1, 4, '2022-10-10', NULL),
(12, 'Miguel', 'Ángel', 'Castro', 'Pinto', 'M', 'CH-20-567', '1990-01-11', 'miguel.castro@example.com', '678-9012', '6789-0123', 'Vía Interamericana, Sector 1, Casa 15, Penonomé', '../uploads/fotos_perfil/original_foto_68813651e14ca.jpg', NULL, '2025-07-23 19:28:50', 0, 4, '2020-03-17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `nombre_departamento` varchar(100) NOT NULL,
  PRIMARY KEY (`id_departamento`),
  UNIQUE KEY `nombre_departamento` (`nombre_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4

--
-- Dumping data for table `departamentos`
--

INSERT INTO `departamentos` (`id_departamento`, `nombre_departamento`) VALUES
(1, 'Recursos Humanos'),
(2, 'Contabilidad'),
(3, 'Tecnología de la Información'),
(4, 'Operaciones');

-- --------------------------------------------------------

--
-- Table structure for table `estatus_colaborador`
--

DROP TABLE IF EXISTS `estatus_colaborador`;
CREATE TABLE IF NOT EXISTS `estatus_colaborador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estatus` enum('Vacaciones','Licencia','Incapacitado','Trabajando') NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4

--
-- Dumping data for table `estatus_colaborador`
--

INSERT INTO `estatus_colaborador` (`id`, `estatus`, `creado_en`) VALUES
(1, 'Vacaciones', '2025-07-24 03:16:51'),
(2, 'Licencia', '2025-07-24 03:16:51'),
(3, 'Incapacitado', '2025-07-24 03:16:51'),
(4, 'Trabajando', '2025-07-24 03:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `ocupaciones`
--

DROP TABLE IF EXISTS `ocupaciones`;
CREATE TABLE IF NOT EXISTS `ocupaciones` (
  `id_ocupacion` int NOT NULL AUTO_INCREMENT,
  `nombre_ocupacion` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ocupacion`),
  UNIQUE KEY `nombre_ocupacion` (`nombre_ocupacion`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4

--
-- Dumping data for table `ocupaciones`
--

INSERT INTO `ocupaciones` (`id_ocupacion`, `nombre_ocupacion`) VALUES
(1, 'Programador'),
(2, 'Electricista'),
(3, 'Contador'),
(4, 'Analista RRHH'),
(5, 'Jefe de Proyectos'),
(6, 'Gerente de TI'),
(7, 'Director de Recursos Humanos'),
(8, 'Gerente de Contabilidad Senior'),
(9, 'Director de Operaciones'),
(10, 'Arquitecto de Software Senior'),
(11, 'Especialista Principal en RRHH'),
(12, 'Coordinador General de Mantenimiento');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4

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
  KEY `usuarios_rol_fk` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `contrasena`, `id_rol`, `activo`) VALUES
(1, 'admin', '$2y$10$S6hryAResvlDYo0n7W7OaOw2kFuRpsMfd1nJNaZQgLSl4qkEMQWuu', 1, 1),
(2, 'Nate', '$2y$10$Mb3iA7gICkTjD7lKhSmn9OQs32TtZlhdNVhvfkadgaJZ8vk1go/la', 2, 1),
(3, 'Maria', '$2y$10$8YJJKT.qtjlkSI8.KQHtmOQiQU3hs2IWvJSsRCMcVzplfTIw3M2/u', 3, 1),
(4, 'Rey', '$2y$10$jb9HZDGEyuf3dUslcbl25e3XCb6bmHwr8/s4B0tk6qRnqaL.XbvCq', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `vacaciones`
--

DROP TABLE IF EXISTS `vacaciones`;
CREATE TABLE IF NOT EXISTS `vacaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `colaborador_id` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vacaciones_fk` (`colaborador_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vacaciones`
--

INSERT INTO `vacaciones` (`id`, `colaborador_id`, `fecha_inicio`, `fecha_fin`) VALUES
(1, 7, '2025-07-30', '2025-08-09');

--
-- Table structure for table `contraloria`
--

DROP TABLE IF EXISTS `contraloria`;
CREATE TABLE IF NOT EXISTS `contraloria`(
    `id` INT auto_increment PRIMARY KEY,
    `nombre_usuario` varchar(50) NULL,
    `contrasena` varchar(255) NOT NULL,
    `activo` INT NOT NULL
)ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD CONSTRAINT `colaborador_fk` FOREIGN KEY (`estatus_id`) REFERENCES `estatus_colaborador` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `colaboradores`
    ADD CONSTRAINT colaboradores_usuarios_id_fk FOREIGN KEY (id_usuario) REFERENCES usuarios (id) ON UPDATE CASCADE ON DELETE CASCADE; ;

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_rol_fk` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `cargos`
--
ALTER TABLE `cargos`
    ADD CONSTRAINT `colaboradores_cargo_fk` FOREIGN KEY (`id_colaborador`) REFERENCES `colaboradores` (`id_colaborador`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `cargos`
    ADD CONSTRAINT `cargos_departamento_fk` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `cargos`
    ADD CONSTRAINT `cargos_ocupacion_fk` FOREIGN KEY (`id_ocupacion`) REFERENCES `ocupaciones` (`id_ocupacion`) ON DELETE RESTRICT ON UPDATE CASCADE;


--
-- Constraints for table `vacaciones`
--
ALTER TABLE `vacaciones`
  ADD CONSTRAINT `vacaciones_fk` FOREIGN KEY (`colaborador_id`) REFERENCES `colaboradores` (`id_colaborador`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
