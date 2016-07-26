-- ============================================================================
-- Copyright (C) 2016 Pablo Martín <pablomreymundo@gmail.com>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- Table of "seguridad_social" for adding catalog content
-- ============================================================================

DELETE FROM llx_seguridad_social WHERE 1;
INSERT INTO llx_seguridad_social (id, description, finance, user_contrib, renov) VALUES 
(122100000,'Silla de ruedas manual no autopropulsable no plegable o rígida', 246, 0, 36),
(122100010,'Silla de ruedas manual no autopropulsable plegable', 340, 0, 36),
(122100011,'Sillla de ruedas manual no autopropulsable plegable para alteraciones funcionales infantiles (no tipo paraguas)', 740, 0, 24),
(122100100,'Silla de ruedas manual autopropulsale no plegable o rígida', 300, 0, 36),
(122100110,'Silla de ruedas manual autopropulsable plegable', 400, 0, 36),
(122127000,'Silla de ruedas eléctrica', 3550, 0, 48),
(122400000,'Asiento', 48, 0, 24),
(122400001,'Respaldo', 85, 0, 24),
(122400002,'Asiento-respaldo postural', 889, 0, 24),
(122400003,'Tapizado de silla de ruedas', 90, 0, 24),
(122400010,'Bandeja desmontable especial', 150, 0, 24),
(122400020,'Freno', 15, 0, 24),
(122400030,'Rueda delantera o pequeña', 20, 0, 24),
(122400031,'Rueda trasera grande', 45, 0, 24),
(122400040,'Batería para silla de ruedas eléctrica', 160, 0, 24),
(122400890,'Chasis o bastidor', 350, 0, 24),
(122400891,'Apoyos posturales para silla de ruedas', 32, 0, 24),
(122400892,'Reposabrazos', 60, 0, 24),
(122400893,'Reposacabeza', 90, 0, 24),
(122400894,'Reposapies', 50, 0, 24),
(122400895,'Doble aro para autpropulsión con un solo brazo', 205, 0, 24),
(060309000,'Órtesis dorso-lumbar semirrígida, estándar', 102, 30.05, 12);
