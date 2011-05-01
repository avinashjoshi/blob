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

blobPageStartup( array( 'authenticated' ) );

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'What\'s on your mind?';
$page[ 'page_id' ] = 'home';
$page [ 'onload' ] = "onLoad=\"document.statusupdate.statusMsg.focus()\"";
blobDatabaseConnect();
$user = blobCurrentUser();
$user_id = blobGetUserID( $user );
if(isset($_POST['btnUpdate'])) {

	if ( $_POST['statusMsg'] == "" ) {
		blobMessagePush( "Status cannot be empty!" );
		blobRedirect( 'index.php' );
	}

	$message = trim($_POST['statusMsg']);

	// Sanitize message input
   $message = stripslashes($message);
   $message = mysql_real_escape_string($message);
   $message = htmlspecialchars($message);

	// Sanitize name input
	$name = stripslashes( $name );
	$name = mysql_real_escape_string($name);

	$query = "INSERT INTO status (user_id, status, date_set) VALUES ('$user_id','$message', NOW());";
	$result = mysql_query($query) or die('<pre>' . mysql_error() . '</pre>' );
}

if(isset($_GET['delete'])) {
	$status_id = $_GET['delete'];
	$status = blobDeleteStatus($status_id);
	blobMessagePush( $status );
	blobRedirect( 'index.php' );
}

$page[ 'body' ] .= "
	<div class=\"body_padded\">
		<h2>What's on your mind?</h2>
		<div class=\"vulnerable_code_area\">
			<form method=\"post\" name=\"statusupdate\">
				<input type=\"hidden\" name=\"index.php\" value=\"index.php\" />
				<table width=\"550\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">
					<tr>
						<td><textarea style=\"padding: 5px;\" name=\"statusMsg\" cols=\"60\" rows=\"3\" maxlength=\"140\"></textarea></td>
					</tr>
					<tr>
						<td><input class=\"button\" name=\"btnUpdate\" type=\"submit\" value=\"Update Status\" > ( Max 140 characters )</td>
					</tr>
				</table>
			</form>
		</div>
		<div class=\"clear\"></div>
		<pre>Your previous status updates:</pre>
		".blobShowUserStatus($user)."
		<br />
	</div>";


blobHtmlEcho( $page );
?>
