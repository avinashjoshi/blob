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

if( isset( $_POST[ 'Login' ] ) ) {

    $user = $_POST[ 'username' ];
    $user = stripslashes( $user );
    $user = mysql_real_escape_string( $user );

    $pass = $_POST[ 'password' ];
    $pass = stripslashes( $pass );
    $pass = mysql_real_escape_string( $pass );
    $pass = md5( $pass );

    $qry = "SELECT * FROM `users` WHERE user='$user' AND password='$pass';";

    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

    if( $result && mysql_num_rows( $result ) == 1 ) {	// Login Successful...
		blobMessagePush( "You have logged in as '".$user."'" );
		blobLogin( $user );
		$row = mysql_fetch_assoc($result);
		if ( $row["isadmin"] == "1" )
			blobAdminLogin();
		blobRedirect( 'index.php' );
    }

    // Login failed
    blobMessagePush( "Login failed" );
    blobRedirect( 'login.php' );
}

$forgotUrl = BLOB_WEB_PAGE_TO_ROOT."forgot.php";

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Login';
$page[ 'page_id' ] = 'login';
$page [ 'onload' ] = "onLoad=\"document.form.username.focus()\"";
$page[ 'body' ] .= "
	<div class=\"body_padded\" align=\"center\">
		<h2>Login</h2>

		<div class=\"main_body_box\" style=\"width: 350px;\">
			<form action=\"login.php\" method=\"post\" name=\"form\"> <fieldset>
			<input type=\"hidden\" name=\"login.php\" value=\"login.php\" />
				<div style=\"float: left\">
					<label for=\"user\">Username</label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"username\"><br />
					<label for=\"pass\">Password</label> <input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\"><br />

					<p align=\"center\"><input class=\"button\" type=\"submit\" value=\"Login\" name=\"Login\">
					<span style=\"margin-left: 10px;\"></span><input class=\"button\" name=\"forgot\" type=\"button\" value=\"Forgot Password?\" onclick=\"window.location='{$forgotUrl}'\"></p>
				</div>
			</fieldset> </form>
		</div>
	</div>";

$right = "
<center><b>New to blob?</b><br>
Easy, free, and instant updates. Get access to the information that interests you most.
<br><br><strong>Create Your Account</strong></center>
<br />
<div class=\"join\">
<form action=\"join.php\">
<input id=\"join\" value=\"Join!\" type=\"submit\">
</form>
</div>";

blobNoLoginHtmlEcho( $page, $right );

?>
