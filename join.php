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

if( isset( $_POST[ 'Join' ] ) ) {

    $fn = $_POST[ 'firstname' ];
    $ln = $_POST[ 'lastname' ];
    $user = $_POST[ 'username' ];
    $pass = $_POST[ 'password' ];
    $key = $_POST[ 'key' ];

    if ( $key == "" || $fn == "" || $ln == "" || $user == "" || $pass == "" || $_FILES["file"]["name"] == "") {
        blobMessagePush( "All fields are compulsary!" );
        if ( $fn != "")
            blobRegMessagePush("fn", $fn);
        if ( $ln != "")
            blobRegMessagePush("ln", $ln);
        if ( $user != "")
            blobRegMessagePush("user", $user);
        if ( $key != "")
            blobRegMessagePush("key", $key);
        blobRedirect( 'join.php' );
    }


    $fn = stripslashes( $fn );
    $fn = mysql_real_escape_string( $fn );

    $ln = stripslashes( $ln );
    $ln = mysql_real_escape_string( $ln );

    $user = stripslashes( $user );
    $user = mysql_real_escape_string( $user );

    $key = stripslashes( $key );
    $key = mysql_real_escape_string( $key );

    $pass = stripslashes( $pass );
    $pass = mysql_real_escape_string( $pass );
    $pass = md5( $pass );

    $qry = "SELECT * FROM `users` WHERE user='$user'";
    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

    if( $result && mysql_num_rows( $result ) == 1 ) {	// Login Successful...
        blobMessagePush( "User '".$user."' already exists. Please Choose a different username" );
        if ( $fn != "")
            blobRegMessagePush("fn", $fn);
        if ( $ln != "")
            blobRegMessagePush("ln", $ln);
        if ( $key != "")
            blobRegMessagePush("key", $key);
        blobRedirect( 'join.php' );
    }

    // Get the base directory for the avatar media...
    $profUrl = $user . '_' . basename( $_FILES['file']['name']);
    $target_path = 'hackable/users/' . $profUrl;

    if ((($_FILES["file"]["type"] == "image/gif")
        || ($_FILES["file"]["type"] == "image/jpeg")
        || ($_FILES["file"]["type"] == "image/png")
        || ($_FILES["file"]["type"] == "image/pjpeg"))
        && ($_FILES["file"]["size"] < 204800)) {
            if ($_FILES["file"]["error"] > 0) {
                blobMessagePush("Apologies, an error has occurred.");
                blobRedirect( 'join.php' );
            } else if(!move_uploaded_file($_FILES["file"]["tmp_name"], $target_path)) {
                blobMessagePush("There was an error uploading the file, please try again!");
                if ( $fn != "")
                    blobRegMessagePush("fn", $fn);
                if ( $ln != "")
                    blobRegMessagePush("ln", $ln);
                if ( $user != "")
                    blobRegMessagePush("user", $user);
                if ( $key != "")
                    blobRegMessagePush("key", $key);
                blobRedirect( 'join.php' );
            }
        } else {
            blobMessagePush("Image type shoud be gif, jpg or png and <b>size</b> less than 200 Kb");
            if ( $fn != "")
                blobRegMessagePush("fn", $fn);
            if ( $ln != "")
                blobRegMessagePush("ln", $ln);
            if ( $user != "")
                blobRegMessagePush("user", $user);
            if ( $key != "")
                blobRegMessagePush("key", $key);
            blobRedirect( 'join.php' );
        }

    $qry = "INSERT INTO users (user_id, first_name, last_name, user, password, sec_key, avatar) VALUES ( 'NULL', '$fn', '$ln', '$user', '$pass', '$key', '{$profUrl}') ;";

    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

    $user_id = blobGetUserID($user);
    $qry = "UPDATE `users` SET follow='$user_id' WHERE user='$user';";
    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );

    if( $result ) {	// Registration Successful...

        blobMessagePush( "You have registered as '".$user."'" );
        blobRedirect( 'login.php' );

    }

    // Registration failed
    blobMessagePush( "Registration failed" );
    blobRedirect( 'join.php' );
}

$fn_pop = blobRegMessagePop("fn");
$ln_pop = blobRegMessagePop("ln");
$user_pop = blobRegMessagePop("user");
$pass_pop = blobRegMessagePop("pass");
$key_pop = blobRegMessagePop("key");

$page = blobPageNewGrab();
$page[ 'title' ] .= $page[ 'title_separator' ].'Join';
$page[ 'page_id' ] = 'join';
$page [ 'onload' ] = "onLoad=\"document.form.firstname.focus()\"";
$page[ 'body' ] .= "                    <div class=\"body_padded\" align=\"center\">
    <h2>Join blob</h2>

    <div class=\"main_body_box\" style=\"width: 350px;\">
    <form action=\"join.php\" name=\"form\" method=\"post\" enctype=\"multipart/form-data\">

    <fieldset>
    <div style=\"float: left\">
    <input type=\"hidden\" size=\"20\" name=\"join.php\" value=\"join.php\">
    <label for=\"firstname\">First Name <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"firstname\" value=\"{$fn_pop}\"><br />
    <label for=\"lastname\">Last Name <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"lastname\" value=\"{$ln_pop}\"><br />
    <label for=\"user\">Username <font color=\"red\">*</font></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"username\" value=\"{$user_pop}\"><br />
    <label for=\"pass\">Password <font color=\"red\">*</font></label> <input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\" value=\"{$pass_pop}\"><br />
    <label for=\"user\">Secret Key <font color=\"red\">*</font> <small>Will be used during password reset</small></label> <input type=\"text\" class=\"loginInput\" size=\"20\" name=\"key\" value=\"{$key_pop}\"><br />
    <label for=\"pass\">Profile Image <font color=\"red\">*</font><small> [Size < 200 kB | type: jpg, png, gif]</small></label> <input type=\"file\" class=\"loginInput\" class=\"button\" name=\"file\" id=\"file\" /><br />
    <p class=\"submit\"><input class=\"button\" type=\"submit\" value=\"Join\" name=\"Join\"></p>
    </div>
    </fieldset>

    </form>

    </div>
    </div>

    ";

$right = "
    <center><b>Already have a blob account?</b><br /><br />
    Easy, free, and instant updates. Get access to the information that interests you most.
    <br><br>
    <div class=\"join\">
    <form action=\"login.php\">
    <input id=\"login\" value=\"Login!\" type=\"submit\">
    </form>
    </div>
    ";

blobNoLoginHtmlEcho( $page, $right );

?>
