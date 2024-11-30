-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-11-2024 a las 02:43:38
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `curieturing_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencias`
--

CREATE TABLE `competencias` (
  `id_competencia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL,
  `tiempo_limite` int(11) NOT NULL COMMENT 'Duración en minutos',
  `creado_por` int(11) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_competencia` enum('Informatica','Fisica','Quimica','Matematicas') NOT NULL DEFAULT 'Informatica'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `competencias`
--

INSERT INTO `competencias` (`id_competencia`, `nombre`, `descripcion`, `fecha_inicio`, `tiempo_limite`, `creado_por`, `creado_en`, `tipo_competencia`) VALUES
(1, 'Hola', '1234', '2025-12-12 23:23:00', 100, 1, '2024-11-29 12:56:59', 'Informatica'),
(3, 'Santicup', 'pampam', '2025-02-23 23:23:00', 100, 1, '2024-11-29 16:36:36', 'Informatica'),
(4, 'Competencia 5', 'DESC 5', '2024-02-23 11:11:00', 100, 1, '2024-11-30 00:57:05', 'Informatica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `competencia_problema`
--

CREATE TABLE `competencia_problema` (
  `id_competencia` int(11) NOT NULL,
  `id_problema` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `competencia_problema`
--

INSERT INTO `competencia_problema` (`id_competencia`, `id_problema`) VALUES
(1, 1),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(3, 1),
(4, 1),
(4, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios`
--

CREATE TABLE `envios` (
  `id_envio` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_problema` int(11) NOT NULL,
  `archivo_ruta` varchar(255) NOT NULL,
  `enviado_en` datetime NOT NULL,
  `resultado` enum('correcto','incorrecto') NOT NULL,
  `puntaje` int(11) NOT NULL,
  `intentos_fallidos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `problemas`
--

CREATE TABLE `problemas` (
  `id_problema` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `puntaje_base` int(11) NOT NULL DEFAULT 100,
  `resultado_esperado` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_problema` enum('Informatica','Fisica','Quimica','Matematicas') NOT NULL DEFAULT 'Informatica'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `problemas`
--

INSERT INTO `problemas` (`id_problema`, `titulo`, `descripcion`, `puntaje_base`, `resultado_esperado`, `creado_en`, `tipo_problema`) VALUES
(1, 'Papu', 'papupapu', 100, '4', '2024-11-29 14:03:51', 'Informatica'),
(2, 'Problema 3', 'DESC 3', 100, 'x=3', '2024-11-30 00:54:50', 'Matematicas'),
(3, 'Problema 4', 'DESC 4', 100, '3', '2024-11-30 00:55:57', 'Informatica'),
(4, 'Problema 6', 'desc 6', 100, '3', '2024-11-30 01:02:12', 'Informatica'),
(5, 'Problema 18', 'Desc 18', 100, '20', '2024-11-30 01:23:22', 'Informatica'),
(6, 'Problema 10', 'desc 10', 200, '20', '2024-11-30 01:29:44', 'Informatica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resultados`
--

CREATE TABLE `resultados` (
  `id_resultado` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_competencia` int(11) NOT NULL,
  `puntaje_total` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resultados`
--

INSERT INTO `resultados` (`id_resultado`, `id_usuario`, `id_competencia`, `puntaje_total`) VALUES
(2, 2, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('administrador','competidor') NOT NULL DEFAULT 'competidor',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `elo_ranking` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `contrasena`, `rol`, `creado_en`, `elo_ranking`) VALUES
(1, 'Paolo', 'emanuel.mamani.quinones2006@gmail.com', '$2y$10$vj7wj2sUVFZ7b.PWD..KDeIKaxKhWQ77ypeAyfPpZPZ3QiyJU0nCm', 'administrador', '2024-11-29 12:56:21', 0),
(2, 'santi', 'santicompe@gmail.com', '$2y$10$GVimnAdsaDUEZcVMbDCt8uwdsLDQ0CVC24H0lzDXFIoHM3HU.i6xO', 'competidor', '2024-11-29 16:30:08', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `competencias`
--
ALTER TABLE `competencias`
  ADD PRIMARY KEY (`id_competencia`),
  ADD KEY `creado_por` (`creado_por`);

--
-- Indices de la tabla `competencia_problema`
--
ALTER TABLE `competencia_problema`
  ADD PRIMARY KEY (`id_competencia`,`id_problema`),
  ADD KEY `id_problema` (`id_problema`);

--
-- Indices de la tabla `envios`
--
ALTER TABLE `envios`
  ADD PRIMARY KEY (`id_envio`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_problema` (`id_problema`);

--
-- Indices de la tabla `problemas`
--
ALTER TABLE `problemas`
  ADD PRIMARY KEY (`id_problema`);

--
-- Indices de la tabla `resultados`
--
ALTER TABLE `resultados`
  ADD PRIMARY KEY (`id_resultado`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_competencia` (`id_competencia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `competencias`
--
ALTER TABLE `competencias`
  MODIFY `id_competencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `envios`
--
ALTER TABLE `envios`
  MODIFY `id_envio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `problemas`
--
ALTER TABLE `problemas`
  MODIFY `id_problema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `resultados`
--
ALTER TABLE `resultados`
  MODIFY `id_resultado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `competencias`
--
ALTER TABLE `competencias`
  ADD CONSTRAINT `competencias_ibfk_1` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `competencia_problema`
--
ALTER TABLE `competencia_problema`
  ADD CONSTRAINT `competencia_problema_ibfk_1` FOREIGN KEY (`id_competencia`) REFERENCES `competencias` (`id_competencia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `competencia_problema_ibfk_2` FOREIGN KEY (`id_problema`) REFERENCES `problemas` (`id_problema`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `envios`
--
ALTER TABLE `envios`
  ADD CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `envios_ibfk_2` FOREIGN KEY (`id_problema`) REFERENCES `problemas` (`id_problema`) ON DELETE CASCADE;

--
-- Filtros para la tabla `resultados`
--
ALTER TABLE `resultados`
  ADD CONSTRAINT `resultados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `resultados_ibfk_2` FOREIGN KEY (`id_competencia`) REFERENCES `competencias` (`id_competencia`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
