-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-09-2025 a las 09:38:42
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
-- Base de datos: `myfoods`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `comentarioid` int(11) NOT NULL,
  `usuarioid` int(11) NOT NULL,
  `publicacionid` int(11) NOT NULL,
  `contenido` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contadorlikes`
--

CREATE TABLE `contadorlikes` (
  `conlikesid` int(11) NOT NULL,
  `likesid` int(11) NOT NULL,
  `cantidadelikes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredientes`
--

CREATE TABLE `ingredientes` (
  `ingredientesid` int(11) NOT NULL,
  `publicacionid` int(11) NOT NULL,
  `ingredientes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `likesid` int(11) NOT NULL,
  `publicacionid` int(11) NOT NULL,
  `usuarioid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pasosreceta`
--

CREATE TABLE `pasosreceta` (
  `pasosreceid` int(11) NOT NULL,
  `publicacionid` int(11) NOT NULL,
  `pasos` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `publicacionid` int(11) NOT NULL,
  `usuarioid` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `fechapubli` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`comentarioid`),
  ADD KEY `fk_comentarios_publicacionid` (`publicacionid`),
  ADD KEY `fk_comentarios_usuarioid` (`usuarioid`);

--
-- Indices de la tabla `contadorlikes`
--
ALTER TABLE `contadorlikes`
  ADD PRIMARY KEY (`conlikesid`),
  ADD KEY `fk_contadorlikes_likesid` (`likesid`);

--
-- Indices de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD PRIMARY KEY (`ingredientesid`),
  ADD KEY `fk_ingedientes_publicacionid` (`publicacionid`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`likesid`),
  ADD KEY `fk_likes_publicacionid` (`publicacionid`),
  ADD KEY `fk_likes_usuarioid` (`usuarioid`);

--
-- Indices de la tabla `pasosreceta`
--
ALTER TABLE `pasosreceta`
  ADD PRIMARY KEY (`pasosreceid`),
  ADD KEY `fk_pasosreceta_publicacionid` (`publicacionid`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`publicacionid`),
  ADD KEY `fk_publicacion_usuarioid` (`usuarioid`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `comentarioid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contadorlikes`
--
ALTER TABLE `contadorlikes`
  MODIFY `conlikesid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  MODIFY `ingredientesid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `likesid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pasosreceta`
--
ALTER TABLE `pasosreceta`
  MODIFY `pasosreceid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `publicacionid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_comentarios_publicacionid` FOREIGN KEY (`publicacionid`) REFERENCES `publicacion` (`publicacionid`),
  ADD CONSTRAINT `fk_comentarios_usuarioid` FOREIGN KEY (`usuarioid`) REFERENCES `usuario` (`idUsuario`);

--
-- Filtros para la tabla `contadorlikes`
--
ALTER TABLE `contadorlikes`
  ADD CONSTRAINT `fk_contadorlikes_likesid` FOREIGN KEY (`likesid`) REFERENCES `likes` (`likesid`);

--
-- Filtros para la tabla `ingredientes`
--
ALTER TABLE `ingredientes`
  ADD CONSTRAINT `fk_ingedientes_publicacionid` FOREIGN KEY (`publicacionid`) REFERENCES `publicacion` (`publicacionid`);

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_likes_publicacionid` FOREIGN KEY (`publicacionid`) REFERENCES `publicacion` (`publicacionid`),
  ADD CONSTRAINT `fk_likes_usuarioid` FOREIGN KEY (`usuarioid`) REFERENCES `usuario` (`idUsuario`);

--
-- Filtros para la tabla `pasosreceta`
--
ALTER TABLE `pasosreceta`
  ADD CONSTRAINT `fk_pasosreceta_publicacionid` FOREIGN KEY (`publicacionid`) REFERENCES `publicacion` (`publicacionid`);

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `fk_publicacion_usuarioid` FOREIGN KEY (`usuarioid`) REFERENCES `usuario` (`idUsuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
