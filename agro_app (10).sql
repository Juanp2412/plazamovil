-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-09-2025 a las 17:41:16
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
-- Base de datos: `agro_app`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agricultor`
--

CREATE TABLE `agricultor` (
  `id_agricultor` int(11) NOT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `id_zona` int(10) DEFAULT NULL,
  `certificaciones` varchar(150) DEFAULT NULL,
  `fotos` int(10) DEFAULT NULL,
  `metodo_entrega` varchar(20) DEFAULT NULL,
  `metodos_de_pago` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `agricultor`
--

INSERT INTO `agricultor` (`id_agricultor`, `id_usuario`, `id_zona`, `certificaciones`, `fotos`, `metodo_entrega`, `metodos_de_pago`) VALUES
(4, 15, 1, NULL, NULL, NULL, NULL),
(5, 16, 1, NULL, NULL, NULL, NULL),
(6, 17, 1, NULL, NULL, NULL, NULL),
(7, 21, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agricultor_calificacion`
--

CREATE TABLE `agricultor_calificacion` (
  `id_agricultor` int(11) NOT NULL,
  `promedio` decimal(3,2) DEFAULT 0.00,
  `total_votos` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificaciones`
--

CREATE TABLE `calificaciones` (
  `id_calificacion` int(2) NOT NULL,
  `id_agricultor` int(10) DEFAULT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `puntuacion` int(2) DEFAULT NULL,
  `comentario` varchar(250) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `fecha_creacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id_carrito`, `id_usuario`, `fecha_creacion`) VALUES
(2, 15, '2025-09-09'),
(4, 18, '2025-09-10'),
(5, 19, '2025-09-10'),
(6, 20, '2025-09-10'),
(7, 12, '2025-09-22'),
(10, 17, '2025-09-22'),
(12, 16, '2025-09-22'),
(17, 22, '2025-09-23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_detalle`
--

CREATE TABLE `carrito_detalle` (
  `id_detalle` int(11) NOT NULL,
  `id_carrito` int(10) DEFAULT NULL,
  `id_producto` int(10) DEFAULT NULL,
  `cantidad` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito_detalle`
--

INSERT INTO `carrito_detalle` (`id_detalle`, `id_carrito`, `id_producto`, `cantidad`) VALUES
(11, 4, 19, 3),
(13, 4, 13, 1),
(14, 4, 20, 5),
(15, 4, 18, 1),
(19, 5, 19, 2),
(20, 5, 20, 1),
(21, 6, 19, 5),
(22, 6, 18, 1),
(44, 17, 13, 1),
(45, 7, 24, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Verduras', 'Variedad de Verduras'),
(2, 'Tuberculos', 'Tuberculos varios'),
(5, 'Frutas', 'Las mejores Frutas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

CREATE TABLE `devoluciones` (
  `id_devolucion` int(10) NOT NULL,
  `id_pedido` int(10) DEFAULT NULL,
  `motivo` varchar(250) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_factura` int(10) NOT NULL,
  `id_venta` int(10) DEFAULT NULL,
  `numero_factura` int(10) DEFAULT NULL,
  `fecha_emision` date DEFAULT NULL,
  `ruta_pdf` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int(10) NOT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `id_agricultor` int(10) DEFAULT NULL,
  `mensaje` varchar(250) DEFAULT NULL,
  `fecha_envio` date DEFAULT NULL,
  `leido` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_venta` int(10) DEFAULT NULL,
  `proveedor` varchar(50) DEFAULT NULL,
  `transaccion_id` varchar(100) DEFAULT NULL,
  `monto` int(20) DEFAULT NULL,
  `moneda` varchar(50) DEFAULT NULL,
  `estado` varchar(20) NOT NULL,
  `metodo` varchar(20) DEFAULT NULL,
  `id_pedido` int(11) NOT NULL,
  `preference_id` varchar(100) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_venta`, `proveedor`, `transaccion_id`, `monto`, `moneda`, `estado`, `metodo`, `id_pedido`, `preference_id`, `fecha_pago`) VALUES
(1, NULL, 'MercadoPago', '2702024581-ee3df1a9-0989-43f3-ace3-8069a3d5d278', 22800, 'COP', 'pendiente', 'checkout', 9, NULL, NULL),
(2, NULL, 'MercadoPago', '2702024581-bf63c4e7-2ada-4ca3-9b08-fa7cf5d97853', 32800, 'COP', 'pendiente', 'checkout', 12, NULL, NULL),
(3, NULL, 'MercadoPago', '2702024581-da45a8e2-83b9-4162-8be1-3bd29037642a', 47000, 'COP', 'pendiente', 'checkout', 13, NULL, NULL),
(4, NULL, 'MercadoPago', '2702024581-6427812c-e521-4d23-942a-e7aaa50ccc20', 47000, 'COP', 'pendiente', 'checkout', 14, NULL, NULL),
(5, NULL, 'MercadoPago', '2702024581-25abaa86-7308-4336-90b3-4fa9b5e53394', 64000, 'COP', 'pendiente', 'checkout', 15, NULL, NULL),
(6, NULL, 'MercadoPago', '2702024581-dbc95999-217a-42ce-ab61-7331d40f698e', 64000, 'COP', 'pendiente', 'checkout', 15, NULL, NULL),
(7, NULL, 'MercadoPago', '2702024581-d8384d1a-79d7-4db2-b173-869fa53a9b37', 64000, 'COP', 'pendiente', 'checkout', 15, NULL, NULL),
(8, NULL, 'MercadoPago', '2702024581-0aff9fcd-4678-4972-b683-958d325690ae', 64000, 'COP', 'pendiente', 'checkout', 16, NULL, NULL),
(9, NULL, 'MercadoPago', '2702024581-2ce385fe-f668-4549-845c-5602ba2adf9a', 64000, 'COP', 'pendiente', 'checkout', 17, NULL, NULL),
(10, NULL, 'MercadoPago', '126621705175', 64000, 'COP', 'approved', 'master', 18, '2702024581-24355a55-735f-4619-afdd-07dd47d87b03', NULL),
(11, NULL, 'MercadoPago', '126624313835', 39800, 'COP', 'approved', 'master', 19, '2702024581-75b8cc4e-569d-4b64-93b7-a2dc33e4ac90', NULL),
(13, NULL, 'MercadoPago', NULL, 8400, 'COP', 'pendiente', 'checkout', 21, '2702024581-e771acc8-9d1c-4625-91eb-c34e74178481', NULL),
(14, NULL, 'MercadoPago', NULL, 8400, 'COP', 'pendiente', 'checkout', 22, '2702024581-4c53b219-06c0-46b6-bccd-0fcea5a46ac7', NULL),
(15, NULL, 'MercadoPago', NULL, 8400, 'COP', 'pendiente', 'checkout', 23, '2702024581-1e9749e1-592b-4547-b5d3-e744b76a2ef2', NULL),
(16, NULL, 'MercadoPago', NULL, 8400, 'COP', 'pendiente', 'checkout', 23, '2702024581-d9c2f597-860f-4c51-8c1a-d69c88785ed8', NULL),
(17, NULL, 'MercadoPago', NULL, 8400, 'COP', 'pendiente', 'checkout', 24, '2702024581-72cc0e0f-8f10-4c4c-9a2c-7bf56e4cf0f1', NULL),
(18, NULL, 'MercadoPago', NULL, 8400, 'COP', 'pendiente', 'checkout', 25, '2702024581-1ae14eff-7c70-4e24-8fca-79cdce55b1dd', NULL),
(22, NULL, 'MercadoPago', '127213921832', 10000, 'COP', 'approved', 'master', 30, '2702024581-07892d58-79ad-4583-8711-e6a80cd0ee6b', NULL),
(23, NULL, 'MercadoPago', '126656058421', 20000, 'COP', 'approved', 'account_money', 31, '2702024581-bbdcd0c5-4125-4a48-9be4-1b5bbdeedd0a', NULL),
(24, NULL, 'MercadoPago', NULL, 22000, 'COP', 'pendiente', 'checkout', 32, '2702024581-3a502b60-f4ac-4857-8199-2937ff48352b', NULL),
(25, NULL, 'MercadoPago', NULL, 17000, 'COP', 'pendiente', 'checkout', 33, '2702024581-e0927434-867d-461a-b9d8-d984df11ffc9', NULL),
(26, NULL, 'MercadoPago', NULL, 17000, 'COP', 'pendiente', 'checkout', 34, '2702024581-d8651227-bcd9-4c5e-9300-deb14acc21d4', NULL),
(27, NULL, 'MercadoPago', NULL, 17000, 'COP', 'pendiente', 'checkout', 34, '2702024581-1376aba5-2154-4ea7-9bea-1a76ded6e224', NULL),
(28, NULL, 'MercadoPago', NULL, 17000, 'COP', 'pendiente', 'checkout', 34, '2702024581-c8022275-80cb-4068-97e2-86e2cd658983', NULL),
(29, NULL, 'MercadoPago', NULL, 145000, 'COP', 'pendiente', 'checkout', 35, '2702024581-ddaabffa-d52f-4965-a357-b594edf0d4c9', NULL),
(30, NULL, 'MercadoPago', NULL, 2000, 'COP', 'pendiente', 'checkout', 36, '2702024581-4ff63599-9f0e-4a1f-8bc8-02c338cb1409', NULL),
(31, NULL, 'MercadoPago', NULL, 2000, 'COP', 'pendiente', 'checkout', 37, '2702024581-714ea429-1781-4f82-8a6e-c6a13ade7ebb', NULL),
(32, NULL, 'MercadoPago', NULL, 2000, 'COP', 'pendiente', 'checkout', 38, '2702024581-85904b33-c535-4858-8161-a686b760fb52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `id_detalle` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha`, `estado`, `id_detalle`) VALUES
(1, 12, '2025-09-18', 'pendiente', NULL),
(2, 12, '2025-09-19', 'pendiente', NULL),
(3, 12, '2025-09-19', 'pendiente', NULL),
(4, 12, '2025-09-19', 'pendiente', NULL),
(5, 12, '2025-09-19', 'pendiente', NULL),
(6, 12, '2025-09-22', 'pendiente', NULL),
(9, 12, '2025-09-22', 'pendiente', NULL),
(10, 19, '2025-09-22', 'pendiente', NULL),
(12, 12, '2025-09-22', 'pendiente', NULL),
(13, 12, '2025-09-22', 'pendiente', NULL),
(14, 12, '2025-09-22', 'pendiente', NULL),
(15, 12, '2025-09-22', 'pendiente', NULL),
(16, 12, '2025-09-22', 'pendiente', NULL),
(17, 12, '2025-09-22', 'pendiente', NULL),
(18, 12, '2025-09-22', 'pagado', NULL),
(19, 22, '2025-09-22', 'pagado', NULL),
(21, 12, '2025-09-22', 'pendiente', NULL),
(22, 12, '2025-09-22', 'pendiente', NULL),
(23, 12, '2025-09-22', 'pendiente', NULL),
(24, 12, '2025-09-22', 'pendiente', NULL),
(25, 12, '2025-09-22', 'pendiente', NULL),
(30, 22, '2025-09-23', 'pagado', NULL),
(31, 22, '2025-09-23', 'pagado', NULL),
(32, 22, '2025-09-23', 'pendiente', NULL),
(33, 22, '2025-09-23', 'pendiente', NULL),
(34, 22, '2025-09-23', 'pendiente', NULL),
(35, 20, '2025-09-23', 'pendiente', NULL),
(36, 12, '2025-09-24', 'pendiente', NULL),
(37, 12, '2025-09-24', 'pendiente', NULL),
(38, 12, '2025-09-24', 'pendiente', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_detalle`
--

CREATE TABLE `pedido_detalle` (
  `id_pedido_detalle` int(11) NOT NULL,
  `id_pedido` int(10) DEFAULT NULL,
  `id_producto` int(10) DEFAULT NULL,
  `cantidad` int(10) DEFAULT NULL,
  `precio_unitario` int(20) DEFAULT NULL,
  `id_unidad` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido_detalle`
--

INSERT INTO `pedido_detalle` (`id_pedido_detalle`, `id_pedido`, `id_producto`, `cantidad`, `precio_unitario`, `id_unidad`) VALUES
(1, 1, 19, 1, 10000, NULL),
(2, 2, 27, 1, 2800, 3),
(3, 3, 27, 1, 2800, 3),
(4, 4, 27, 1, 2800, 3),
(5, 5, 27, 1, 2800, 3),
(6, 6, 27, 1, 2800, 3),
(11, 9, 27, 1, 2800, 3),
(12, 9, 18, 1, 10000, 4),
(13, 9, 19, 1, 10000, 2),
(14, 10, 19, 2, 10000, 2),
(15, 10, 20, 1, 5000, 2),
(19, 12, 27, 1, 2800, 3),
(20, 12, 18, 1, 10000, 4),
(21, 12, 19, 2, 10000, 2),
(22, 13, 19, 3, 10000, 2),
(23, 13, 13, 1, 17000, 2),
(24, 14, 19, 3, 10000, 2),
(25, 14, 13, 1, 17000, 2),
(26, 15, 19, 3, 10000, 2),
(27, 15, 13, 2, 17000, 2),
(28, 16, 19, 3, 10000, 2),
(29, 16, 13, 2, 17000, 2),
(30, 17, 19, 3, 10000, 2),
(31, 17, 13, 2, 17000, 2),
(32, 18, 19, 3, 10000, 2),
(33, 18, 13, 2, 17000, 2),
(34, 19, 19, 2, 10000, 2),
(35, 19, 13, 1, 17000, 2),
(36, 19, 27, 1, 2800, 3),
(38, 21, 27, 3, 2800, 3),
(39, 22, 27, 3, 2800, 3),
(40, 23, 27, 3, 2800, 3),
(41, 24, 27, 3, 2800, 3),
(42, 25, 27, 3, 2800, 3),
(47, 30, 19, 1, 10000, 2),
(48, 31, 22, 3, 5000, 3),
(49, 31, 23, 1, 5000, 2),
(50, 32, 21, 1, 2000, 3),
(51, 32, 25, 3, 5000, 2),
(52, 32, 22, 1, 5000, 3),
(53, 33, 13, 1, 17000, 2),
(54, 34, 13, 1, 17000, 2),
(55, 35, 19, 5, 15000, 2),
(56, 35, 18, 1, 70000, 4),
(57, 36, 24, 1, 2000, 3),
(58, 37, 24, 1, 2000, 3),
(59, 38, 24, 1, 2000, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pqrs`
--

CREATE TABLE `pqrs` (
  `id_pqrs` int(11) NOT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `respuesta` varchar(250) DEFAULT NULL,
  `fecha_respuesta` date DEFAULT NULL,
  `adjunto` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pqrs`
--

INSERT INTO `pqrs` (`id_pqrs`, `id_usuario`, `tipo`, `asunto`, `descripcion`, `fecha`, `estado`, `respuesta`, `fecha_respuesta`, `adjunto`) VALUES
(1, 12, 'queja', 'queja', 'me quejo', '2025-09-22', 'respondido', 'repuesta', '2025-09-23', NULL),
(2, 12, 'peticion', 'proyecto listo ', 'para manana', '2025-09-22', 'respondido', 'ya esta listo', '2025-09-22', NULL),
(3, 22, 'queja', 'quejarme', 'me duele el pie', '2025-09-23', 'respondido', 'no sea sapa ', '2025-09-22', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_agricultor` int(10) DEFAULT NULL,
  `id_categoria` int(10) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `stock` int(20) DEFAULT NULL,
  `precio_unitario` int(10) DEFAULT NULL,
  `id_unidad` int(10) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `fecha_publicacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_agricultor`, `id_categoria`, `descripcion`, `nombre`, `stock`, `precio_unitario`, `id_unidad`, `foto`, `fecha_publicacion`) VALUES
(13, 4, 1, 'mango manguito', 'Mango  ', 497, 17000, 2, '68be360ba62ad_mango tomi.webp', '2025-09-10'),
(18, 4, 2, 'papa pareja ideal para salar, acompañamiento perfecto para los asados del domingo después de misa.', 'papa', 500, 70000, 4, '68c0fab3a72f3_mucha papa.jpg', '2025-09-23'),
(19, 4, 5, 'freza directamente traída del campo, la mejor freza de la región..', 'freza', 500, 15000, 2, '68c1184d6de9d_freza.jpeg', '2025-09-10'),
(20, 6, 2, 'los mejores frijoles bola roja que te puedes comer', 'frijoles', 998, 5000, 2, '68c119e129caa_3000.jpg', '2025-09-10'),
(21, 7, 5, 'Limones verdes', 'Limon', 499, 2000, 3, '68c1706eaa8a2_Limon-600x473.jpg', '2025-09-10'),
(22, 7, 1, 'Muy moradas', 'Berenjenas', 297, 5000, 3, '68c170dd7391a_Berenjena-1.jpg', '2025-09-10'),
(23, 7, 1, 'Zanahorias', 'Zanahorias', 1999, 5000, 2, '68c1712b65810_carousel1.jpg', '2025-09-10'),
(24, 7, 2, 'Bola Roja', 'Frijoles', 498, 2000, 3, '68c17175c5a3a_68c0fadc338e9_pexels-arina-krasnikova-6316686.jpg', '2025-09-10'),
(25, 7, 1, 'Esta verde', 'Brocoli', 300, 5000, 2, '68c171b00328b_68c126723ccea_pexels-cup-of-couple-7657091.jpg', '2025-09-10'),
(26, 7, 5, 'Se llaman naranjas a pesar de que son amarillas', 'Naranjas', 500, 6000, 2, '68c174f9eaeb4_carousel3.jpg', '2025-09-10'),
(27, 7, 5, 'Son rojas y ricas', 'Manzanas', 299, 2800, 3, '68c1759173cb0_Manzanas.jpg', '2025-09-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_resenas`
--

CREATE TABLE `producto_resenas` (
  `id_resena` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `estrellas` int(11) NOT NULL,
  `comentario` varchar(250) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_resenas`
--

INSERT INTO `producto_resenas` (`id_resena`, `id_producto`, `id_usuario`, `estrellas`, `comentario`, `fecha`) VALUES
(1, 22, 22, 1, 'Excelente calidad', '2025-09-23 08:26:48'),
(2, 22, 22, 5, 'excelente producto', '2025-09-23 08:27:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reembolsos`
--

CREATE TABLE `reembolsos` (
  `id_reembolso` int(10) NOT NULL,
  `id_pago` int(10) DEFAULT NULL,
  `monto` int(20) DEFAULT NULL,
  `fecha_envio` date DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(2) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Comprador'),
(3, 'Agricultor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_de_medida`
--

CREATE TABLE `unidades_de_medida` (
  `id_unidad` int(11) NOT NULL,
  `nombre` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_de_medida`
--

INSERT INTO `unidades_de_medida` (`id_unidad`, `nombre`) VALUES
(2, 'kilogramos'),
(3, 'libra'),
(4, 'bultos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_rol` int(2) DEFAULT NULL,
  `numero_documento` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `tipo_documento` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` varchar(50) DEFAULT NULL,
  `Foto` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `email`, `telefono`, `id_rol`, `numero_documento`, `username`, `password`, `tipo_documento`, `fecha_nacimiento`, `Foto`) VALUES
(12, 'admin', 'ujiht3d5@gmil.com', '35345345', 1, '1209930219', 'admin ', '$2y$10$esrEadfU98MFN5aL7DK7JutaraCsgmEXGVxAkItGUZfckGoSBrIve', 'Cédula de Ciudadanía', '2025-09-23', NULL),
(15, 'juan', 'ujiht3d5@gmail.com', '2147483647', 3, '80821459', 'juanvende', '$2y$10$62LFc9It39LEQDsF3l8blelHDyWW7PzyXNa1y/K5wHAGbQyqidVrW', 'Cédula de Ciudadanía', '2025-09-15', '68c09b902aff3_papa.jpg'),
(16, 'juan', 'dslajdla@gmail.com', '312321433', 3, '1212343214', 'Juanagro', '$2y$10$k37KuW.hQKb5clt4FoZzAOpoV08BjCDJpuiO2HKoLZw3JiW5UTF3W', 'Cédula de Ciudadanía', '2025-09-25', '68c10a8aa4f85_papa.jpg'),
(17, 'usuario1', 'asdfafds@gmail.com', '324363454', 3, '1243214214', 'usuario1', '$2y$10$2gZyn6wFGrZNTN6WcUPuUe4mIHCy80XK5s7tQlnXV4BTBqQlRYjga', 'Cédula de Ciudadanía', '2025-09-10', NULL),
(18, 'comprador', 'jsdijasiujdq2@gmail.com', '12324314', 2, '9827979842', 'comprador', '$2y$10$v.2ByRZi0CSLQzhfPBwSaewdcU1IO55gJxTwVVdWQSFqyu6T2k11G', 'Pasaporte', '2025-09-10', NULL),
(19, 'usuario2', 'asdsadas@gmail.com', '124221412', 2, '421423142', 'usuario2', '$2y$10$3P5oEuHTHYa0Da7v5jhdmOliqGnsivtvnymQnXuIb1lhMJIHqVLI.', 'Cédula de Ciudadanía', '2025-09-10', NULL),
(20, 'Juan Felipe Prado Yemayusa', 'felixxd722@gmail.com', '3003889780', 1, '1053323936', 'Felpa Admin', '$2y$10$ktpqrELucIH9bEqzJNDdpu0MOlSeL6wrpq0r4fTDBkddebWRb6gRW', 'Cédula de Ciudadanía', '2004-06-23', '68c16e6594559_68bee552b2243_perfil_2_1752074078.jpeg'),
(21, 'Felipe Prado', 'pradoyemayusafelipe@gmail.com', '3225003395', 3, '1053323936', 'Felpa Vendedor', '$2y$10$6fEK3Al34uRYT9K39ewtqujgQUTYovQr8AZ6vc2MS0GEpb81UMu.y', 'Cédula de Ciudadanía', '2022-06-23', NULL),
(22, 'Laura valentina Mosquera quintana', 'lauquintana1201@gmail.com', '3102330520', 2, '1028492317', 'valen1201', '$2y$10$fdh7NVZL4Cu/HSP.koHP5.gp0JJhKvUYgPhZueBMUTFvzezNrbcTq', 'Otro', '2010-01-12', '68d1d5a451fe7_papa.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(10) NOT NULL,
  `id_pedido` int(10) DEFAULT NULL,
  `id_agricultor` int(10) DEFAULT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `fecha_venta` date DEFAULT NULL,
  `subtotal` int(20) DEFAULT NULL,
  `impuestos` double(10,2) DEFAULT NULL,
  `total` double(20,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zona`
--

CREATE TABLE `zona` (
  `id_zona` int(10) NOT NULL,
  `zona` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zona`
--

INSERT INTO `zona` (`id_zona`, `zona`) VALUES
(1, 'Zona por defecto');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agricultor`
--
ALTER TABLE `agricultor`
  ADD PRIMARY KEY (`id_agricultor`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_zona` (`id_zona`);

--
-- Indices de la tabla `agricultor_calificacion`
--
ALTER TABLE `agricultor_calificacion`
  ADD PRIMARY KEY (`id_agricultor`);

--
-- Indices de la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD PRIMARY KEY (`id_calificacion`),
  ADD KEY `id_agricultor` (`id_agricultor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `carrito_detalle`
--
ALTER TABLE `carrito_detalle`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_carrito` (`id_carrito`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD PRIMARY KEY (`id_devolucion`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_venta` (`id_venta`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_agricultor` (`id_agricultor`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `fk_pagos_pedido` (`id_pedido`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD PRIMARY KEY (`id_pedido_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_unidad` (`id_unidad`);

--
-- Indices de la tabla `pqrs`
--
ALTER TABLE `pqrs`
  ADD PRIMARY KEY (`id_pqrs`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_agricultor` (`id_agricultor`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `producto_resenas`
--
ALTER TABLE `producto_resenas`
  ADD PRIMARY KEY (`id_resena`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `reembolsos`
--
ALTER TABLE `reembolsos`
  ADD PRIMARY KEY (`id_reembolso`),
  ADD KEY `id_pago` (`id_pago`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `unidades_de_medida`
--
ALTER TABLE `unidades_de_medida`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_agricultor` (`id_agricultor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `zona`
--
ALTER TABLE `zona`
  ADD PRIMARY KEY (`id_zona`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agricultor`
--
ALTER TABLE `agricultor`
  MODIFY `id_agricultor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `carrito_detalle`
--
ALTER TABLE `carrito_detalle`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  MODIFY `id_pedido_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `pqrs`
--
ALTER TABLE `pqrs`
  MODIFY `id_pqrs` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `producto_resenas`
--
ALTER TABLE `producto_resenas`
  MODIFY `id_resena` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `unidades_de_medida`
--
ALTER TABLE `unidades_de_medida`
  MODIFY `id_unidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `agricultor`
--
ALTER TABLE `agricultor`
  ADD CONSTRAINT `agricultor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `agricultor_ibfk_2` FOREIGN KEY (`id_zona`) REFERENCES `zona` (`id_zona`);

--
-- Filtros para la tabla `agricultor_calificacion`
--
ALTER TABLE `agricultor_calificacion`
  ADD CONSTRAINT `agricultor_calificacion_ibfk_1` FOREIGN KEY (`id_agricultor`) REFERENCES `agricultor` (`id_agricultor`);

--
-- Filtros para la tabla `calificaciones`
--
ALTER TABLE `calificaciones`
  ADD CONSTRAINT `calificaciones_ibfk_1` FOREIGN KEY (`id_agricultor`) REFERENCES `agricultor` (`id_agricultor`),
  ADD CONSTRAINT `calificaciones_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `carrito_detalle`
--
ALTER TABLE `carrito_detalle`
  ADD CONSTRAINT `carrito_detalle_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`),
  ADD CONSTRAINT `carrito_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `devoluciones`
--
ALTER TABLE `devoluciones`
  ADD CONSTRAINT `devoluciones_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`);

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_agricultor`) REFERENCES `agricultor` (`id_agricultor`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `pedido_detalle`
--
ALTER TABLE `pedido_detalle`
  ADD CONSTRAINT `pedido_detalle_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `pedido_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `pedido_detalle_ibfk_3` FOREIGN KEY (`id_unidad`) REFERENCES `unidades_de_medida` (`id_unidad`);

--
-- Filtros para la tabla `pqrs`
--
ALTER TABLE `pqrs`
  ADD CONSTRAINT `pqrs_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_agricultor`) REFERENCES `agricultor` (`id_agricultor`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`);

--
-- Filtros para la tabla `producto_resenas`
--
ALTER TABLE `producto_resenas`
  ADD CONSTRAINT `producto_resenas_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `producto_resenas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `reembolsos`
--
ALTER TABLE `reembolsos`
  ADD CONSTRAINT `reembolsos_ibfk_1` FOREIGN KEY (`id_pago`) REFERENCES `pagos` (`id_pago`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`id_agricultor`) REFERENCES `agricultor` (`id_agricultor`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
