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

define( 'BLOB_WEB_PAGE_TO_ROOT', '' );
require_once BLOB_WEB_PAGE_TO_ROOT.'blob/includes/blobPage.inc.php';

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Setup';
$page[ 'page_id' ] = 'setup';

if( isset( $_POST[ 'create_db' ] ) ) {

	if ($DBMS == 'MySQL') {
		include_once BLOB_WEB_PAGE_TO_ROOT.'blob/includes/DBMS/MySQL.php';
	}
	else {
		blobMessagePush( "ERROR: Invalid database selected. Please review the config file syntax." );
		blobPageReload();
	}

}


$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Database setup <img src=\"".BLOB_WEB_PAGE_TO_ROOT."blob/images/spanner.png\"></h1>

	<p>Click on the 'Create / Reset Database' button below to create or reset your database. If you get an error make sure you have the correct user credentials in /config/config.inc.php</p>

	<p>If the database already exists, it will be cleared and the data will be reset.</p>

	<br />

	Backend Database: <b>".$DBMS."</b>

	<br /><br /><br />

	<!-- Create db button -->
<div class=\"join\">
	<form action=\"setup.php\" method=\"post\">
		<input name=\"create_db\" type=\"submit\" value=\"Create / Reset Database\">
	</form>
</div>
</div>
";

$right = "
<center><strong>New to blob?</strong></center>
<br />
<div class=\"join\">
<form action=\"join.php\">
<input id=\"join\" value=\"Join!\" type=\"submit\">
</form>
</div><br />
<center><b>Already have a blob account?</b><br /><br />
<div class=\"join\">
<form action=\"login.php\">
<input id=\"login\" value=\"Login!\" type=\"submit\">
</form>
</div>
<br /><br />Easy, free, and instant updates. Get access to the information that interests you most.
";

if (blobIsLoggedIn())
  blobHtmlEcho( $page );
else
  blobNoLoginHtmlEcho( $page, $right );

?>
