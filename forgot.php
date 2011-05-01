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
blobPageStartup( array( 'notauthenticated' ) );

blobDatabaseConnect();

if( isset( $_POST[ 'EditPassword' ] ) ) {

	$user = $_POST[ 'username' ];
	$pass = $_POST[ 'password' ];
	$key = $_POST[ 'key' ];

	if ( $user == "" || $pass == "" || $key == "" ) {
		blobMessagePush( "All fields are compulsory!" );
		blobRedirect( 'forgot.php' );
	}

	$user = stripslashes( $user );
	$user = mysql_real_escape_string( $user );
	$pass = stripslashes( $pass );
	$pass = mysql_real_escape_string( $pass );
	$key= stripslashes( $key );
	$key = mysql_real_escape_string( $key );

	if ( blobExistUser($user) ) {
		$old_key = getSecKey($user);
		if ( $old_key == $key ) {
			$pass = md5( $pass );
			$qry = "UPDATE `users` SET password='$pass' WHERE user='$user';";
			$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
			blobMessagePush( "Password changed!" );
			blobRedirect( 'login.php' );
		} else {
			blobMessagePush("Security Key does not match!");
		}
	} else {
		blobMessagePush("This user does not exist!");
	}

	blobRedirect( 'forgot.php' );
}

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Forgot Password';
$page[ 'page_id' ] = 'forgotpass';
$page [ 'onload' ] = "onLoad=\"document.form.username.focus()\"";
$page[ 'body' ] .= "
	<div class=\"body_padded\" align=\"center\">
		<h2>Forgot Password!</h2>
		<div class=\"main_body_box\" style=\"width: 400px;\">
			<form action=\"forgot.php\" name=\"form\" method=\"post\" enctype=\"multipart/form-data\"> <fieldset>
			<input type=\"hidden\" name=\"forgot.php\" value=\"forgot.php\" />
				<div style=\"float: left\">
					<label for=\"username\">User Name <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"username\" value=\"\"><br />
				</div>
				<div style=\"float: left\">
					<label for=\"pass\">New Password <font color=\"red\">*</font></label> <input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\">
				</div>
				<div style=\"float: left\">
					<label for=\"key\">Secret Key <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"key\">
				</div>
				<div style=\"float: left; width: 100%;\">
					<p class=\"submit\"><input class=\"button\" type=\"submit\" value=\"Change Password\" name=\"EditPassword\"></p>
				</div>
			</fieldset> </form>
		</div>
	</div>";

$right = "
<center><strong>New to blob?</strong></center>
<br />
<div class=\"join\">
<form action=\"register.php\">
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

blobNoLoginHtmlEcho( $page, $right );

?>
