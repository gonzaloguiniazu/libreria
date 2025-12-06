-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-12-2025 a las 03:31:40
-- Versión del servidor: 10.4.8-MariaDB
-- Versión de PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `libreria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `domicilio` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre`, `apellido`, `domicilio`, `email`, `contrasena`) VALUES
(1, 'Gonzalo', 'Guinazu', 'calle falsa 123', 'gonzaloguiniazu@gmail.com', '$2y$10$18w671eDH7H.4bbxKweVwe7fCfg8tYsk2ZoZr6RCxGn/pfM8kquD6'),
(2, 'maria', 'asd', 'asd123', 'asd123@gmail.com', '$2y$10$focmEvhSeTABFhARNXpjfOPwK2kfXbLPqI5JO6hjnm2QMwMB7S5ae'),
(3, 'admin', 'admin', 'admin123', 'admin@admin', '$2y$10$cVfq3auvfz89kwqNOKAS/uZ9cbqDcqRwxh1xk1pKx23wuOxNGYbzi'),
(4, 'gonzalo', 'sinapellido', 'ahre123', 'gonzaloguiniazu@hotmail.com', '$2y$10$Mu3URayKL/jm6XJlVguvAOy32qRvCVzvaMdPOuVp34jWSh7AOWWdi'),
(5, 'mayra', 'altuna', 'asd123', 'mayra@maira', '$2y$10$jVTWMh0AeZmQ/QbkYARMX.y0A40xwSi4Lf3r9aFw3haYSzRJLL7fy'),
(6, 'Juan', 'gallo', 'sincalle', 'juan@juan', '$2y$10$YvKO19IjJhoDuvyNs8cCHuNGsFK2amXDrbWJLOeIm3NOORcGkE.Zi'),
(7, 'juan', 'sito', 'sincalle', 'juan@sito', '$2y$10$3p.ds1Wwt7Qf2xYQ963uUOJKx72ubfEVFPhwBOFi8igpGu9mS4mC6'),
(8, 'loro', 'loro', 'sincalle', 'loro@loro', '$2y$10$UnkT6FwXcKd0NZxDTJWIgOhUTARVlp3Q9JLOWmS84AatUFJyX4FSy');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id_detalle` int(11) NOT NULL,
  `id_venta` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id_detalle`, `id_venta`, `id_producto`, `precio`, `cantidad`) VALUES
(1, 1, 18, '1000.00', 2),
(2, 1, 16, '300.00', 3),
(3, 2, 17, '600.00', 2),
(4, 3, 17, '600.00', 1),
(5, 4, 16, '300.00', 2),
(6, 5, 18, '1000.00', 2),
(7, 5, 19, '1200.00', 1),
(8, 6, 18, '1000.00', 1),
(9, 7, 18, '1000.00', 2),
(10, 7, 19, '1200.00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int(11) NOT NULL,
  `nombre_marca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id_marca`, `nombre_marca`) VALUES
(11, 'rivadavia'),
(12, 'bic'),
(13, 'ezco'),
(14, 'simball'),
(15, 'pizzini'),
(16, 'maped'),
(17, 'giotto'),
(18, 'avon'),
(19, 'exito'),
(20, 'gloria'),
(21, 'bulit'),
(22, 'filgo'),
(24, 'laprida'),
(25, 'faber-castell'),
(26, 'paper mate'),
(27, 'parker'),
(28, 'sharpie'),
(29, 'voligoma'),
(30, 'pelikan'),
(31, 'Uhu'),
(32, 'el nene'),
(33, 'america');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `precio` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `descripcion`, `id_marca`, `stock`, `stock_minimo`, `precio`, `imagen`) VALUES
(16, 'escuadra 25cm', 16, 10, 2, 300, 'imagenes/escuadra-maped.jpg'),
(17, 'regla 30cm', 15, 10, 2, 600, 'imagenes/regla-pizzini-30cm.jpg'),
(18, 'cuaderno', 19, 9, 3, 1000, 'imagenes/cuaderno-exito.jpg'),
(19, 'boligrafo negro', 27, 8, 3, 1200, 'imagenes/boligrafo-parker.jpg'),
(20, 'Silicona Uhu', 31, 10, 2, 1500, 'imagenes/adhesivo_uhu.jpg'),
(21, 'Lapicera x 10 colores pelikan', 30, 10, 2, 0, 'imagenes/bol_pelikan.jpg'),
(22, 'block hojas blanco n5 el nene', 32, 9, 3, 2000, 'imagenes/block-blanco.jpg'),
(23, 'block hojas color n5 el nene', 32, 12, 3, 2200, 'imagenes/block-color.jpg'),
(24, 'compas pizzini', 15, 10, 3, 2500, 'imagenes/compas-pizzini.jpg'),
(25, 'cuaderno gloria tapa dura 42 hojas', 20, 12, 4, 800, 'imagenes/cuaderno-gloria.jpg'),
(26, 'crayones x12 varios colores', 16, 15, 1, 1500, 'imagenes/crayones-x12-maped.jpg'),
(27, 'goma blanca maped', 16, 20, 5, 1200, 'imagenes/goma-blanca-maped.jpg'),
(28, 'lapiz negro x12 bic', 12, 20, 3, 2800, 'imagenes/lapic-negro-x12-bic.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'completada',
  `direccion_envio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_cliente`, `fecha_venta`, `total`, `estado`, `direccion_envio`) VALUES
(1, 4, '2025-11-29 22:21:38', '2900.00', 'completada', NULL),
(2, 4, '2025-11-30 16:40:26', '1200.00', 'completada', NULL),
(3, 4, '2025-11-30 16:40:48', '600.00', 'completada', NULL),
(4, 4, '2025-11-30 16:44:30', '600.00', 'completada', NULL),
(5, 4, '2025-11-30 16:53:02', '3200.00', 'completada', NULL),
(6, 4, '2025-11-30 16:53:26', '1000.00', 'completada', NULL),
(7, 4, '2025-11-30 19:39:09', '3200.00', 'completada', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `idx_producto_cantidad` (`id_producto`,`cantidad`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_marca` (`id_marca`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `idx_cliente_fecha` (`id_cliente`,`fecha_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`),
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
