-- Pasanaku — Seed Data
SET NAMES utf8mb4;

-- Admin: admin@pasanaku.com / admin123
INSERT INTO `admins` (`email`, `password_hash`, `nombre`) VALUES
('admin@pasanaku.com', '$2y$10$q.Z7li7gKM60UQlxKEViduf3KxQXLmQYhO7VAkc6mXPs6RE5FR32.', 'Administrador');

-- Personas
INSERT INTO `personas` (`nombre`, `telefono`) VALUES
('María Quispe',    '77712345'),
('Juan Condori',    '76523890'),
('Rosa Mamani',     '60098712'),
('Carlos Flores',   '71234567'),
('Ana Torrez',      '78901234'),
('Pedro Gutiérrez', '69087654'),
('Lucia Vargas',    '77765432'),
('Sergio Ríos',     '71199988');

-- Pasanakus
INSERT INTO `pasanakus` (`nombre`, `monto_contribucion`, `periodo`, `fecha_inicio`, `estado`) VALUES
('Pasanaku Oficina',  100.00, 'semanal',  '2025-01-15', 'activo'),
('Pasanaku Familia',  500.00, 'mensual',  '2025-01-01', 'activo'),
('Pasanaku Vecinos 2024', 200.00, 'mensual', '2024-02-01', 'finalizado');

-- Pasanaku 1 (Oficina) — 4 participantes
INSERT INTO `pasanaku_participantes` (`pasanaku_id`, `persona_id`, `orden`, `activo`) VALUES
(1, 1, 1, 1),  -- María Quispe
(1, 2, 2, 1),  -- Juan Condori
(1, 3, 3, 1),  -- Rosa Mamani
(1, 4, 4, 1);  -- Carlos Flores

-- Pasanaku 2 (Familia) — 6 participantes
INSERT INTO `pasanaku_participantes` (`pasanaku_id`, `persona_id`, `orden`, `activo`) VALUES
(2, 1, 1, 1),  -- María Quispe
(2, 3, 2, 1),  -- Rosa Mamani
(2, 5, 3, 1),  -- Ana Torrez
(2, 6, 4, 1),  -- Pedro Gutiérrez
(2, 7, 5, 1),  -- Lucia Vargas
(2, 8, 6, 1);  -- Sergio Ríos

-- Pasanaku 3 (Vecinos 2024) — 3 participantes
INSERT INTO `pasanaku_participantes` (`pasanaku_id`, `persona_id`, `orden`, `activo`) VALUES
(3, 4, 1, 1),  -- Carlos Flores
(3, 2, 2, 1),  -- Juan Condori
(3, 5, 3, 1);  -- Ana Torrez

-- Pagos ronda 1 Pasanaku 1 (Oficina)
INSERT INTO `pagos` (`pasanaku_id`, `participante_id`, `ronda`, `monto`, `fecha_pago`) VALUES
(1, 1, 1, 100.00, '2025-01-15'),
(1, 2, 1, 100.00, '2025-01-15'),
(1, 3, 1, 100.00, '2025-01-16'),
(1, 4, 1, 100.00, '2025-01-16');

-- Pagos ronda 2 Pasanaku 1
INSERT INTO `pagos` (`pasanaku_id`, `participante_id`, `ronda`, `monto`, `fecha_pago`) VALUES
(1, 1, 2, 100.00, '2025-01-22'),
(1, 2, 2, 100.00, '2025-01-22'),
(1, 3, 2, 100.00, '2025-01-23'),
(1, 4, 2, 100.00, '2025-01-24');

-- Pagos parciales ronda 3 Pasanaku 1
INSERT INTO `pagos` (`pasanaku_id`, `participante_id`, `ronda`, `monto`, `fecha_pago`) VALUES
(1, 1, 3, 100.00, '2025-01-29'),
(1, 2, 3, 100.00, '2025-01-29');

-- Entrega ronda 1 y 2 Pasanaku 1
INSERT INTO `entregas` (`pasanaku_id`, `participante_id`, `ronda`, `fecha_entrega`) VALUES
(1, 1, 1, '2025-01-16'),
(1, 2, 2, '2025-01-24');

-- Pagos ronda 1 Pasanaku 2 (Familia)
INSERT INTO `pagos` (`pasanaku_id`, `participante_id`, `ronda`, `monto`, `fecha_pago`) VALUES
(2, 5, 1, 500.00, '2025-01-01'),
(2, 6, 1, 500.00, '2025-01-02'),
(2, 7, 1, 500.00, '2025-01-03'),
(2, 8, 1, 500.00, '2025-01-04'),
(2, 9, 1, 500.00, '2025-01-05'),
(2, 10, 1, 500.00, '2025-01-06');

-- Entrega ronda 1 Pasanaku 2
INSERT INTO `entregas` (`pasanaku_id`, `participante_id`, `ronda`, `fecha_entrega`) VALUES
(2, 5, 1, '2025-01-07');

-- Pasanaku 3 (Vecinos 2024) — todos completados
INSERT INTO `pagos` (`pasanaku_id`, `participante_id`, `ronda`, `monto`, `fecha_pago`) VALUES
(3, 11, 1, 200.00, '2024-02-01'), (3, 12, 1, 200.00, '2024-02-01'), (3, 13, 1, 200.00, '2024-02-01'),
(3, 11, 2, 200.00, '2024-03-01'), (3, 12, 2, 200.00, '2024-03-01'), (3, 13, 2, 200.00, '2024-03-01'),
(3, 11, 3, 200.00, '2024-04-01'), (3, 12, 3, 200.00, '2024-04-01'), (3, 13, 3, 200.00, '2024-04-01');

INSERT INTO `entregas` (`pasanaku_id`, `participante_id`, `ronda`, `fecha_entrega`) VALUES
(3, 11, 1, '2024-02-05'),
(3, 12, 2, '2024-03-05'),
(3, 13, 3, '2024-04-05');
