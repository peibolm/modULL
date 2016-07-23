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

create table llx_seguridad_social
(
  code			integer(9) PRIMARY KEY,
  description		tinytext,
  finance		double(8,2) NOT NULL,
  user_contrib		double(8,2)
)ENGINE=innodb;
