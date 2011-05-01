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

### MySQL ###
if ($DBMS == 'MySQL') {
 $DBMS = htmlspecialchars(strip_tags($DBMS));
 $DBMS_errorFunc = mysql_error();

 function escapeString( $var ) {
  $var = mysql_real_escape_string( $var );
  return $var;
 }

 function db_login( $user,$pass )  {
  $login = "SELECT * FROM `users` WHERE user='$user' AND password='$pass';";

	$result = @mysql_query($login) or die('<pre>' . mysql_error() . '</pre>' );

	if( $result && mysql_num_rows( $result ) == 1 ) {	// Login Successful...
		blobMessagePush( "You have logged in as '".$user."'" );
		blobLogin( $user );
		blobRedirect( 'index.php' );
		}
 }
}
### END MySQL ###

### INVALID DBMS ###
else {
 $DBMS = "No DBMS selected.";
 $DBMS_errorFunc = '';
}
### END INVALID ###

$DBMS_connError = '<div align="center">
		<img src="'.BLOB_WEB_PAGE_TO_ROOT.'blob/images/logo.png">
		<pre>Unable to connect to the database.<br>'.$DBMS_errorFunc.'<br /><br /></pre>
		Click <a href="'.BLOB_WEB_PAGE_TO_ROOT.'setup.php">here</a> to setup the database.
		</div>';

function blobDatabaseConnect() {
	global $_BLOB;
	global $DBMS;
	global $DBMS_connError;

	if ($DBMS == 'MySQL') {
		if( !@mysql_connect( $_BLOB[ 'db_server' ], $_BLOB[ 'db_user' ], $_BLOB[ 'db_password' ] )
		|| !@mysql_select_db( $_BLOB[ 'db_database' ] ) ) {
			die( $DBMS_connError );
		}
	}
}

// -- END

?>
