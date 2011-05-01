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
blobPageStartup( array( 'authenticated' ) );

blobDatabaseConnect();
$user = blobCurrentUser();
$profile = blobLoadProfile();
$avatar = getAvatar($user);
$key = getSecKey($user);

if( isset( $_POST[ 'DeleteProfile' ] ) ) {
	$qry = "DELETE FROM `users` WHERE user='$user';";
	$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
	blobMessagePush( "Profile $user is deleted!<br /> Thank you for using blob!" );
	blobRedirect( '../logout.php' );
}

if( isset( $_POST[ 'EditProfile' ] ) ) {
	$fn = $_POST[ 'firstname' ];
	$ln = $_POST[ 'lastname' ];
	$pass = $_POST[ 'password' ];
	$key_new = $_POST[ 'key' ];

	if ( $fn == "" || $ln == "" || $key_new == "" ) {
		blobMessagePush( "First name, last name and key compulsory!" );
		blobRedirect( 'edit.php' );
	}

	$fn = stripslashes( $fn );
	$fn = mysql_real_escape_string( $fn );
	$ln = stripslashes( $ln );
	$ln = mysql_real_escape_string( $ln );
	$key_new = stripslashes( $key_new );
	$key_new = mysql_real_escape_string( $key_new );

	if ( $_FILES['file']['name'] != "" ) {
		$profUrl = basename( $_FILES['file']['name']);
		$target_path = '../hackable/users/' . basename( $_FILES['file']['name']);
		if ($_FILES["file"]["error"] > 0) {
			blobMessagePush("Apologies, an error has occurred.");
			blobRedirect( 'edit.php' );
		} else {
			if(!move_uploaded_file($_FILES["file"]["tmp_name"], $target_path)) {
				blobMessagePush("There was an error uploading the file, please try again!");
				blobRedirect( 'edit.php' );
			}
		}
		$qry = "UPDATE `users` SET avatar='$profUrl' WHERE user='$user';";
		$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
		blobMessagePush( "Updated the new Image!" );
	}

	if ( $profile["fn"] != $fn || $profile["ln"] != $ln || $key_new != $key ) {
		$qry = "UPDATE `users` SET first_name='$fn', last_name='$ln', sec_key='$key_new' WHERE user='$user';";
		$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
		blobMessagePush( "Profile updated" );
	} else {
		blobMessagePush( "Profile was not updated" );
	}

	if ( $pass != "" ) {
		$pass = stripslashes( $pass );
		$pass = mysql_real_escape_string( $pass );
		$pass = md5( $pass );
		$qry = "UPDATE `users` SET password='$pass' WHERE user='$user';";
		$result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
		blobMessagePush( "Password changed. You Need to re-login" );
		blobRedirect( '../logout.php' );
	}

	blobRedirect( 'edit.php' );
}

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Edit Profile: '.$user;
$page[ 'page_id' ] = 'editprofile';
$page [ 'onload' ] = "onLoad=\"document.form.firstname.focus()\"";
$page ['script'] .= "<script language=\"javascript\">function doEnable(){ document.form.key.readOnly=false; document.form.key_edit_btn.disabled=true; }</script>";
$page[ 'body' ] .= "
<div class=\"body_padded\" align=\"center\">
                        <h2>Edit Profile: {$user}</h2>

                        <div class=\"main_body_box\" style=\"width: 400px;\">
                            <form action=\"edit.php\" name=\"form\" method=\"post\" enctype=\"multipart/form-data\">
                            <input type=\"hidden\" name=\"edit.php\" value=\"edit.php\" />

	<fieldset>
	<div style=\"float: left\">
			<label for=\"firstname\">First Name <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"firstname\" value=\"{$profile["fn"]}\"><br />
			</div>
			<div style=\"float: left\">
			<label for=\"lastname\">Last Name <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"lastname\" value=\"{$profile["ln"]}\"><br />
			</div>
			<div style=\"float: left\">
			<label for=\"pass\">Password <font color=\"red\">(Not changed if blank)</font></label> <input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\">
			</div>
			<div style=\"float: left\">
			<label for=\"key\">Secret Key <font color=\"red\"><input type=\"button\" value=\"Edit\" name=\"key_edit_btn\" onclick=\"doEnable()\"></font></label> <input type=\"text\" readonly class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" id=\"key\" name=\"key\" value=\"{$key}\">
			</div>
			<div style=\"float: left; padding-right: 10px; border-right: 2px solid #C0C0C0;\">
				<img src=\"{$avatar}\" width=\"100\" />
			</div>
			<div style=\"float: left; margin-left: 120px; margin-top: -70px;\">
				<label for=\"pass\">Profile Image <font color=\"red\"><br />(Not changed if blank)</font></label> <br /><input type=\"file\" class=\"loginInput\" class=\"button\" name=\"file\" id=\"file\" /><br />
			</div>
			<div style=\"float: left; width: 100%;\">
			<p class=\"submit\"><input class=\"button\" type=\"submit\" value=\"Edit Profile\" name=\"EditProfile\">&nbsp;&nbsp;
			<input class=\"button\" type=\"submit\" value=\"Delete Profile\" name=\"DeleteProfile\"></p>
			</div>
	</fieldset>

	</form>

                        </div>
</div>

";

blobHtmlEcho( $page );

?>
