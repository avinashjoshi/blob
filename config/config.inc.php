<?php

/*
 * blob is a micro-blogging service where you can share notices
 * about yourself with friends, family, and colleagues!
 *
 * Copyright (C) 2011  Avinash Joshi <avinashtjoshi@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

# Database management system to use Currently, we support only MySQL. Will try to provide support for other databases in the future

$DBMS = 'MySQL';

# Database variables

$_BLOB = array();
$_BLOB[ 'db_server' ] = 'localhost';
$_BLOB[ 'db_database' ] = 'blob';
$_BLOB[ 'db_user' ] = 'root';
$_BLOB[ 'db_password' ] = 'toor';

?>
