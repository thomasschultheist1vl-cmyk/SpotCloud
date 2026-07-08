CREATE DATABASE IF NOT EXISTS `spotcloud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `spotcloud`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-07-2026 a las 03:36:35
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
-- Base de datos: `spotcloud`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `albumes`
--

CREATE TABLE `albumes` (
  `id_album` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `anio` int(11) DEFAULT NULL,
  `id_artista` int(11) DEFAULT NULL,
  `ruta_portada` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `albumes`
--

INSERT INTO `albumes` (`id_album`, `titulo`, `anio`, `id_artista`, `ruta_portada`) VALUES
(1, 'Count Your Blessings', 2006, 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Portadas/cyb.jpg'),
(2, 'Vulgar Display of Power', 1992, 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Portadas/vdop.jpg'),
(3, 'Nightmare', 2010, 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Portadas/nightmare.jpg'),
(4, 'With Ears to See and Eyes to Hear', 2010, 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Portadas/wetsaeth.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artistas`
--

CREATE TABLE `artistas` (
  `id_artista` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `biografia_corta` text DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `artistas`
--

INSERT INTO `artistas` (`id_artista`, `nombre`, `biografia_corta`, `genero`) VALUES
(1, 'Bring Me The Horizon', 'Banda británica de Sheffield.', 'Deathcore / Metalcore'),
(2, 'Pantera', 'Leyendas del groove metal de Texas.', 'Groove Metal'),
(3, 'Avenged Sevenfold', 'Banda originaria de Huntington Beach, California.', 'Heavy Metal'),
(4, 'Sleeping With Sirens', 'Banda de post-hardcore de Florida.', 'Post-Hardcore');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

CREATE TABLE `canciones` (
  `id_cancion` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `duracion` time DEFAULT NULL,
  `id_album` int(11) DEFAULT NULL,
  `ruta_archivo_mp3` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canciones`
--

INSERT INTO `canciones` (`id_cancion`, `titulo`, `duracion`, `id_album`, `ruta_archivo_mp3`) VALUES
(1, 'Pray for Plagues', '04:21:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/pray_for_plagues.mp3'),
(2, 'Tell Slater Not to Wash His Dick', '03:30:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/tell_slater.mp3'),
(3, 'Mouth for War', '00:03:57', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/mouth_for_war.mp3'),
(4, 'Walk', '00:05:14', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/walk.mp3'),
(5, 'Nightmare', '06:14:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/nightmare.mp3'),
(6, 'Welcome to the Family', '04:05:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/welcome_family.mp3'),
(7, 'If I\'m James Dean, You\'re Audrey Hepburn', '03:39:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/james_dean.mp3'),
(8, 'With Ears to See, and Eyes to Hear', '03:43:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/ears_to_see.mp3'),
(9, 'For Stevie Wonder\'s Eyes Only (Braille)', '04:29:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/braille.mp3'),
(10, 'A Lot Like Vegas', '02:09:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/a_lot_like_vegas.mp3'),
(11, 'Black & Blue', '04:33:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/black_and_blue.mp3'),
(12, 'Slow Dance', '01:16:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/slow_dance.mp3'),
(13, 'Liquor & Love Lost', '02:39:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/liquor_and_love_lost.mp3'),
(14, '(I Used to Make Out With) Medusa', '05:38:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/i_used_to_make_out.mp3'),
(15, 'Fifteen Fathoms, Counting', '01:56:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/fifteen_fathoms.mp3'),
(16, 'Off the Heezay', '05:38:00', 1, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Count%20your%20blessings/off_the_heezay.mp3'),
(17, 'A New Level', '03:57:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/a_new_level.mp3'),
(18, 'Fucking Hostile', '02:48:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/fucking_hostile.mp3'),
(19, 'This Love', '06:33:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/this_love.mp3'),
(20, 'Rise', '04:36:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/rise.mp3'),
(21, 'No Good (Attack the Radical)', '04:49:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/no_good.mp3'),
(22, 'Live in a Hole', '05:00:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/live_in_a_hole.mp3'),
(23, 'Regular People (Conceit)', '05:27:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/regular_people.mp3'),
(24, 'By Demons Be Driven', '04:40:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/by_demons_be_driven.mp3'),
(25, 'Hollow', '05:45:00', 2, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Vulgar%20Display%20Of%20Power/hollow.mp3'),
(26, 'Danger Line', '05:28:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/danger_line.mp3'),
(27, 'Buried Alive', '06:44:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/buried_alive.mp3'),
(28, 'Natural Born Killer', '05:15:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/natural_born_killer.mp3'),
(29, 'So Far Away', '05:26:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/so_far_away.mp3'),
(30, 'God Hates Us', '05:19:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/god_hates_us.mp3'),
(31, 'Victim', '07:29:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/victim.mp3'),
(32, 'Tonight the World Dies', '04:41:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/tonight_the_world_dies.mp3'),
(33, 'Fiction', '05:07:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/fiction.mp3'),
(34, 'Save Me', '10:56:00', 3, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/Nightmare/save_me.mp3'),
(35, 'The Bomb Dot Com V2.0', '03:31:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/the_bomb_dot_com.mp3'),
(36, 'You Kill Me (In A Good Way)', '03:42:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/you_kill_me.mp3'),
(37, 'Let Love Bleed Red', '03:42:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/let_love_bleed_red.mp3'),
(38, 'Captain Tyin Knots vs. Mr. Walkway (No Way)', '03:22:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/captain_tyin_knots.mp3'),
(39, 'Don\'t Fall Asleep At The Helm', '02:14:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/dont_fall_asleep.mp3'),
(40, 'In Case of Emergency, Dial 411', '02:44:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/in_case_of_emergency.mp3'),
(41, 'The Left Side of Everywhere', '02:59:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/the_left_side_of_everywhere.mp3'),
(42, 'Dance Party', '01:32:00', 4, 'https://hblcxonrrtgjxwelqyjl.supabase.co/storage/v1/object/public/Canciones/Albumes%20MP3/With%20Ears%20to%20See%20and%20Eyes%20to%20Hear/dance_party.mp3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cola_canciones`
--

CREATE TABLE `cola_canciones` (
  `id_cola` int(11) NOT NULL,
  `id_cancion` int(11) NOT NULL,
  `orden` int(11) NOT NULL,
  `reproducida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cola_reproduccion`
--

CREATE TABLE `cola_reproduccion` (
  `id_cola` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial`
--

CREATE TABLE `historial` (
  `id_historial` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_cancion` int(11) DEFAULT NULL,
  `fecha_reproduccion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `password`, `foto_perfil`) VALUES
(1, 'Mateo', 'mateo@spotcloud.com', '123456', NULL),
(2, 'Morena', 'morena@spotcloud.com', '654321', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_artistas`
--

CREATE TABLE `usuarios_artistas` (
  `id_usuario` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `albumes`
--
ALTER TABLE `albumes`
  ADD PRIMARY KEY (`id_album`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Indices de la tabla `artistas`
--
ALTER TABLE `artistas`
  ADD PRIMARY KEY (`id_artista`);

--
-- Indices de la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD PRIMARY KEY (`id_cancion`),
  ADD KEY `id_album` (`id_album`);

--
-- Indices de la tabla `cola_canciones`
--
ALTER TABLE `cola_canciones`
  ADD PRIMARY KEY (`id_cola`,`id_cancion`),
  ADD KEY `id_cancion` (`id_cancion`);

--
-- Indices de la tabla `cola_reproduccion`
--
ALTER TABLE `cola_reproduccion`
  ADD PRIMARY KEY (`id_cola`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `historial`
--
ALTER TABLE `historial`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_cancion` (`id_cancion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `usuarios_artistas`
--
ALTER TABLE `usuarios_artistas`
  ADD PRIMARY KEY (`id_usuario`,`id_artista`),
  ADD KEY `id_artista` (`id_artista`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `albumes`
--
ALTER TABLE `albumes`
  MODIFY `id_album` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `artistas`
--
ALTER TABLE `artistas`
  MODIFY `id_artista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `canciones`
--
ALTER TABLE `canciones`
  MODIFY `id_cancion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `cola_reproduccion`
--
ALTER TABLE `cola_reproduccion`
  MODIFY `id_cola` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial`
--
ALTER TABLE `historial`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `albumes`
--
ALTER TABLE `albumes`
  ADD CONSTRAINT `albumes_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id_artista`) ON DELETE CASCADE;

--
-- Filtros para la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD CONSTRAINT `canciones_ibfk_1` FOREIGN KEY (`id_album`) REFERENCES `albumes` (`id_album`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cola_canciones`
--
ALTER TABLE `cola_canciones`
  ADD CONSTRAINT `cola_canciones_ibfk_1` FOREIGN KEY (`id_cola`) REFERENCES `cola_reproduccion` (`id_cola`) ON DELETE CASCADE,
  ADD CONSTRAINT `cola_canciones_ibfk_2` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id_cancion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cola_reproduccion`
--
ALTER TABLE `cola_reproduccion`
  ADD CONSTRAINT `cola_reproduccion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial`
--
ALTER TABLE `historial`
  ADD CONSTRAINT `historial_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_ibfk_2` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id_cancion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios_artistas`
--
ALTER TABLE `usuarios_artistas`
  ADD CONSTRAINT `usuarios_artistas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_artistas_ibfk_2` FOREIGN KEY (`id_artista`) REFERENCES `artistas` (`id_artista`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
