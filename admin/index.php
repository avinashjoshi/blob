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

define( 'BLOB_WEB_PAGE_TO_ROOT', '../' );
require_once BLOB_WEB_PAGE_TO_ROOT.'blob/includes/blobPage.inc.php';

blobPageStartup( array( 'authenticated', 'admin' ) );

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Admin Area';
$page[ 'page_id' ] = 'admin';
$page[ 'script' ] = '<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery(".content").hide();
  //toggle the componenet with class msg_body
  jQuery(".heading").click(function()
  {
    jQuery(this).next(".content").slideToggle(500);
  });
});
</script>';

blobDatabaseConnect();
$user = blobCurrentUser();
$user_id = blobGetUserID( $user );

if(isset($_POST['updateLevel'])) {
	$ln = $_POST[ 'levelNumber' ];
	$un = $_POST[ 'username' ];

	$ln = stripslashes( $ln );
	$ln = mysql_real_escape_string( $ln );

	$un = stripslashes( $un );
	$un = mysql_real_escape_string( $un );

	if ( $un == "" ) {
		blobMessagePush( "Please enter a User name!" );
		blobRedirect( "." );
	} else if ( !blobExistUser($un) ){
		blobMessagePush( "This username does not exist!" );
		blobRedirect( "." );
	} else if ( $un == $user ) {
		blobMessagePush( "That's You!" );
		blobRedirect( "." );
	} else {
		$qry = "UPDATE `users` SET isadmin='$ln' WHERE user='$un';";
		$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
		$level = $ln == "1" ? "Admin" : "Normal User";
		blobMessagePush( "User '$un' is now '$level'" );
		blobRedirect( '.' );
	}
}

if(isset($_POST['deleteUser'])) {
	$un = $_POST[ 'username' ];

	$un = stripslashes( $un );
	$un = mysql_real_escape_string( $un );

	if ( $un == "" ) {
		blobMessagePush( "Please enter a User name!" );
		blobRedirect( "." );
	} else if ( !blobExistUser($un) ){
		blobMessagePush( "This username does not exist!" );
		blobRedirect( "." );
	} else if ( $un == $user ) {
		blobMessagePush( "That's You!" );
		blobRedirect( "." );
	} else {
		$qry = "DELETE FROM `users` WHERE user='$un';";
		$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
		blobMessagePush( "User '$un' deleted!" );
		blobRedirect( '.' );
	}
}

$userList = blobGetSiteUsers();
$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h2>Admin Area</h2>

	<hr><center>
	<div class=\"table-wrap\">
	<p class=\"heading\"><b>User List [+/-]</b></p>
	<div class=\"content\">
	{$userList}
	</div>
	</div>
	</center><hr>
	<br />
	<div class=\"vulnerable_code_area\">
		<form method=\"post\" name=\"updateuserlevel\">
			<h3>Update User level</h3>
			<label for=\"user\">Username</label> <input type=\"text\" class=\"loginInput\" style=\"width:220px;\" size=\"10\" name=\"username\">
			<span><select class=\"button\" name=\"levelNumber\"><option value=\"0\">Normal User</option><option value=\"1\">Admin</option></select></span>
			<input class=\"button\" name=\"updateLevel\" type=\"submit\" value=\"Update Level\" \">
		</form>
	</div>

	<div class=\"vulnerable_code_area\">
		<form method=\"post\" name=\"deleteUser\">
			<h3>Delete User</h3>
			<label for=\"user\">Username</label> <input type=\"text\" class=\"loginInput\" style=\"width:220px;\" size=\"10\" name=\"username\">
			<span style=\"margin-left: 20px;\"></span>
			<input class=\"button\" name=\"deleteUser\" type=\"submit\" value=\"Delete User\" \">
		</form>
	</div>

	<div class=\"clear\"></div>


	<br />

</div>
";


blobHtmlEcho( $page );
?>
