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

if( !defined( 'BLOB_WEB_PAGE_TO_ROOT' ) ) {
    define( 'BLOB System error- WEB_PAGE_TO_ROOT undefined' );
    exit;
}

session_start(); // Creates a 'Full Path Disclosure'.

// Include configs
require_once BLOB_WEB_PAGE_TO_ROOT.'config/config.inc.php';

// Declare the $html variable
if(!isset($html)){
    $html = "";
}

// BLOB version
function blobVersionGet() {
    return '0.9a';
}

// BLOB release date
function blobReleaseDateGet() {
    return 'May 01 2011';
}

// Start session functions --
function &blobSessionGrab() {
    if( !isset( $_SESSION[ 'blob' ] ) ) {
        $_SESSION[ 'blob' ] = array();
    }
    return $_SESSION[ 'blob' ];
}

function blobPageStartup( $pActions ) {
    if( in_array( 'authenticated', $pActions ) ) {
        if( !blobIsLoggedIn()){
            blobRedirect( BLOB_WEB_PAGE_TO_ROOT.'login.php' );
        }
    }
    if( in_array( 'notauthenticated', $pActions ) ) {
        if( blobIsLoggedIn()){
            blobMessagePush( "You are logged in!" );
            blobRedirect( BLOB_WEB_PAGE_TO_ROOT.'index.php' );
        }
    }
    if( in_array( 'admin', $pActions ) ) {
        if( !blobIsAdmin()){
            blobMessagePush( "You are not admin!" );
            blobRedirect( BLOB_WEB_PAGE_TO_ROOT.'index.php' );
        }
    }
}

function blobLogin( $pUsername ) {
    $blobSession =& blobSessionGrab();
    $blobSession['username'] = $pUsername;
}

function blobAdminLogin() {
    $blobSession =& blobSessionGrab();
    $blobSession['admin'] = true;
}

function blobIsLoggedIn() {
    $blobSession =& blobSessionGrab();
    return isset( $blobSession['username'] );
}

function blobIsAdmin() {
    $blobSession =& blobSessionGrab();
    return isset( $blobSession['admin'] );
}

function blobLogout() {
    $blobSession =& blobSessionGrab();
    unset( $blobSession['username'] );
    unset( $blobSession['admin'] );
}

function blobPageReload() {
    blobRedirect( $_SERVER[ 'PHP_SELF' ] );
}

function blobCurrentUser() {
    $blobSession =& blobSessionGrab();
    return ( isset( $blobSession['username']) ? $blobSession['username'] : '') ;
}

function getAvatar( $user ) {
    $query  = "SELECT avatar FROM users where user = '$user'";
    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    return (BLOB_WEB_PAGE_TO_ROOT. 'hackable/users/' . $row[0]);
}

function blobGetUserFullName( $user ) {
    $query  = "SELECT first_name, last_name FROM users where user = '$user'";
    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    return ($row[0] . ' ' . $row[1]);
}

function blobGetUserID( $user ) {
    $query  = "SELECT user_id FROM users where user = '$user'";
    $result = mysql_query($query);
    $row = mysql_fetch_row($result);
    return ($row[0]);
}

function blobExistUser( $user ){
    $qry = "SELECT * FROM `users` WHERE user='$user';";
    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
    if( $result && mysql_num_rows( $result ) == 1 ) {	// User Exist...
        return ( true );
    } else {
        return ( false );
    }
}

function blobLoadProfile( $user=null ) {
    if ( is_null($user) ) {
        $user = blobCurrentUser();
    }
    $query  = "SELECT first_name, last_name, avatar FROM users where user = '$user'";
    $result = @mysql_query($query) or die('<pre>' . mysql_error() . '</pre>' );
    if( $result && mysql_num_rows( $result ) == 1 ) {
        $row = mysql_fetch_row($result);
        $returnArray = array(
            'fn' => $row[0],
            'ln' => $row[1],
            'avatar' => $row[3],
        );
        return $returnArray;
    } else {
        return ( null );
    }

}

function getSecKey( $user=null ) {
    if ( is_null($user) ) {
        $user = blobCurrentUser();
    }
    $query  = "SELECT sec_key FROM users where user = '$user'";
    $result = @mysql_query($query) or die('<pre>' . mysql_error() . '</pre>' );
    if( $result && mysql_num_rows( $result ) == 1 ) {
        $row = mysql_fetch_assoc($result);
        return $row["sec_key"];
    } else {
        return ( null );
    }

}

function blobCanFollowHTML( $toFollowUser ) {
    $follower = blobCurrentUser();
    if( blobCanFollow($toFollowUser) ) {
        $followUrl = BLOB_WEB_PAGE_TO_ROOT . "profile/follow.php?user=" . $toFollowUser;
        return ("<input class=\"button\" name=\"btnUpdate\" type=\"submit\" value=\"Follow\" onclick=\"window.location='{$followUrl}'\">");
    } else if ( $toFollowUser == $follower ) {
        return ( "<span class=\"button-plain\" style=\"color: rgb(153, 204, 51);\">That's me!</span>" );
    } else {
        return ( "<span class=\"button-plain\">Following!</span>" );
    }
}

function blobCanFollow( $toFollowUser ) {
    $toFollow = blobGetUserID($toFollowUser);
    $follower = blobCurrentUser();
    $qry = "SELECT follow FROM `users` WHERE user='$follower';";
    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
    $row = mysql_fetch_row($result);
    $follow = explode(",", $row[0]);
    if( in_array($toFollow,$follow) ) {
        return ( false );
    } else {
        return ( true );

    }
}

function blobFollowUser( $toFollowUser ) {
    $toFollow = blobGetUserID($toFollowUser);
    $follower = blobCurrentUser();
    if ( blobCanFollow($toFollowUser) ) {
        $qry = "SELECT follow FROM `users` WHERE user='$follower';";
        $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
        $row = mysql_fetch_assoc($result);
        $follow = $row["follow"] . "," . $toFollow;
        $qry = "UPDATE `users` SET follow='$follow' WHERE user='$follower';";
        $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
        return ( "You are now following '{$toFollowUser}'!" );
    } else if ( $toFollowUser == $follower ) {
        return ( "That's me!" );
    } else {
        return ( "You are already following '{$toFollowUser}'!" );
    }
}


function blobRedirect( $pLocation ) {
    session_commit();
    header( "Location: {$pLocation}" );
    exit;
}

// Get user list HTML
function blobUserList(){
    $query  = "SELECT first_name,last_name,user,avatar comment FROM users";
    $result = mysql_query($query);
    $userList = '';
    while($row = mysql_fetch_row($result)){
        $fullName    = $row[0] . ' ' . $row[1];
        $profilepage = BLOB_WEB_PAGE_TO_ROOT . 'profile/view.php?user=' . $row[2];
        $profileUrl = blobInternalLinkUrlGet($profilepage,$fullName);
        $avatar = getAvatar($row[2]);
        $avatarImage = "<img src=\"{$avatar}\" width=\"100\" />";
        $followHTML = blobCanFollowHTML($row[2]);
        $userList .= "
            <div class=\"user-list\">
            <div style=\"float: left; padding-right: 10px; border-right: 2px solid #C0C0C0; height: 100px;\">
    {$avatarImage}
    </div>
    <div style=\"margin-left: 120px;\">
    {$profileUrl}
    <br /><br />
    {$followHTML}
    </div>
    </div>";
    }
    return $userList;
}

function blobGetSiteUsers() {
    $query  = "SELECT * FROM users";
    $result = mysql_query($query);
    $userList = '<table id="mytable" cellspacing="0" summary="Comments" align="center">';
    $userList .= '<tr><th>User Name</th><th>First Name</th><th>Last Name</th><th width="70">Is Admin?</th><th>Avatar</th></tr>';
    while($row = mysql_fetch_assoc($result)){
        $profilepage = BLOB_WEB_PAGE_TO_ROOT . 'profile/view.php?user=' . $row["user"];
        $profileUrl = blobInternalLinkUrlGet($profilepage,$row["user"]);
        $userList .= '<tr>';
        $userList .= '<td>' . $profileUrl . '</td>';
        $userList .= '<td>' . $row["first_name"] . '</td>';
        $userList .= '<td>' . $row["last_name"] . '</td>';
        $userList .= '<td>' . $row["isadmin"] . '</td>';
        $userList .= '<td>' . $row["avatar"] . '</td>';
        $userList .= '</tr>';
    }
    $userList .= '</table>';
    return $userList;
}

function blobDeleteStatus ( $status_id ) {
    $user = blobCurrentUser();
    $user_id = blobGetUserID($user);
    $qry = "SELECT status_id, user_id FROM `status` WHERE user_id='$user_id' AND status_id='$status_id';";
    $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
    if( $result && mysql_num_rows( $result ) == 1 ) {
        $qry = "DELETE FROM `status` WHERE user_id='$user_id' AND status_id='$status_id';";
        $result = @mysql_query($qry) or die('<pre>' . mysql_error() . '</pre>' );
        return ( "This status has been deleted" );
    } else {
        return ( "This Status Does not exist!" );
    }
}

function blobShowUserStatus( $user ) {
    $user_id = blobGetUserID( $user );
    $query  = "SELECT status, date_set, status_id FROM status where user_id = '$user_id' ORDER BY date_set DESC";
    $result = mysql_query($query);
    $status = '';
    if ( $result && mysql_num_rows( $result ) > 0 ) {
        while($row = mysql_fetch_row($result)){
            $statusMsg = $row[0];
            $time   = date("g:i a F j, Y ", strtotime($row[1]));
            $statusId = $row[2];
            $deleteLink = BLOB_WEB_PAGE_TO_ROOT . "index.php?delete={$statusId}";
            $delete = "<div style=\"float: right;\"><a href=\"{$deleteLink}\" style=\"text-decoration: none;\">X</a></div>";
            $deleteHTML = blobCurrentUser() == $user ? "{$delete}" : "";
            $status .= "<div id=\"comments_main\"><div id=\"comments\">{$deleteHTML}<pre width=\"77\"><b>{$user}</b> {$statusMsg}</pre> <br />" . "</div> <span style=\"float: right; font-weight: bold; font-style: italic; font-size: 10px;\">@ {$time} IST</span></div> <br />";
        }
    } else {
        $thisUser = blobCurrentUser() == $user ? "you have" : "this user has";
        $status = "<div id=\"comments_main\"><div id=\"comments\"><pre width=\"77\">Oops! \nLooks like {$thisUser} not yet updated any status! :(</pre> </div></div>";
    }
    return $status;
}
// -- END

function &blobPageNewGrab() {
    $returnArray = array(
        'title' => 'BLOB v'.blobVersionGet().'',
        'title_separator' => ' :: ',
        'body' => '',
        'page_id' => '',
        'help_button' => '',
        'source_button' => '',
        'onload' => '',
        'script' => '',
    );
    return $returnArray;
}

// Start message functions for registration page only --
function blobRegMessagePush( $id, $pMessage ) {
    $blobSession =& blobSessionGrab();
    if( !isset( $blobSession[ 'regMessages' ] ) ) {
        $blobSession[ 'regMessages' ] = array();
    }
    $blobSession[ 'regMessages' ][$id] = $pMessage;
}

function blobRegMessagePop( $id ) {
    $blobSession =& blobSessionGrab();
    if( !isset( $blobSession[ 'regMessages' ] ) || count( $blobSession[ 'regMessages' ] ) == 0 ) {
        return false;
    }
    $retVal = $blobSession[ 'regMessages' ][$id];
    unset($blobSession[ 'regMessages' ][$id]);
    return ( $retVal );
}

function messagesRegPopAllToHtml() {
    $messagesHtml = '';
    while( $message = blobMessagePop() ) {	// TODO- sharpen!
        $messagesHtml .= "<div class=\"message\">{$message}</div>";
    }
    return $messagesHtml;
}
// --END

function getQuote() {
    $link = BLOB_WEB_PAGE_TO_ROOT . 'about.php';
    $link = "<a href=\"$link\" style=\"color: #99cc33;\">Read More</a>";
    $quote = "<font color=\"#99cc33\">blob</font> is a micro-blogging service. This is a Free Software. Join now to share notices about yourself with friends, family, and colleagues! ({$link})";
    return ($quote);
}

// Start message functions --
function blobMessagePush( $pMessage ) {
    $blobSession =& blobSessionGrab();
    if( !isset( $blobSession[ 'messages' ] ) ) {
        $blobSession[ 'messages' ] = array();
    }
    $blobSession[ 'messages' ][] = $pMessage;
}

function blobMessagePop() {
    $blobSession =& blobSessionGrab();
    if( !isset( $blobSession[ 'messages' ] ) || count( $blobSession[ 'messages' ] ) == 0 ) {
        return false;
    }
    return array_shift( $blobSession[ 'messages' ] );
}

function messagesPopAllToHtml() {
    $messagesHtml = '';
    while( $message = blobMessagePop() ) {	// TODO- sharpen!
        $messagesHtml .= "<div class=\"message\">{$message}</div>";
    }
    return $messagesHtml;
}
// --END

function blobHtmlEcho( $pPage ) {

    $menuBlocks = array();

    $menuBlocks['profile'] = array();
    $menuBlocks['profile'][] = array( 'id' => 'viewprofile', 'name' => 'View Profile', 'url' => 'profile/view.php' );
    $menuBlocks['profile'][] = array( 'id' => 'editprofile', 'name' => 'Edit Profile', 'url' => 'profile/edit.php' );
    $menuBlocks['profile'][] = array( 'id' => 'othersprofile', 'name' => 'View Users', 'url' => 'profile/follow.php' );

    if ( blobIsAdmin()) {
        $menuBlocks['admin'] = array();
        $menuBlocks['admin'][] = array( 'id' => 'setup', 'name' => 'Setup', 'url' => 'setup.php' );
    }

    $menuHtml = '';

    foreach( $menuBlocks as $menuBlock ) {
        $menuBlockHtml = '';
        foreach( $menuBlock as $menuItem ) {
            $selectedClass = ( $menuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
            $fixedUrl = BLOB_WEB_PAGE_TO_ROOT.$menuItem['url'];
            $menuBlockHtml .= "<li onclick=\"window.location='{$fixedUrl}'\" class=\"{$selectedClass}\"><a href=\"{$fixedUrl}\">{$menuItem['name']}</a></li>";
        }
        $menuHtml .= "<ul>{$menuBlockHtml}</ul>";
    }
    $adminLink = "";
    //Primary Menu
    $pmenuBlocks = array();
    $pmenuBlocks[] = array( 'id' => 'home', 'name' => 'Home', 'url' => '.' );
    if ( blobIsAdmin()) {
        $adminLink = BLOB_WEB_PAGE_TO_ROOT . 'admin';
        $adminLink = blobInternalLinkUrlGet( $adminLink, "Admin");
        $pmenuBlocks[] = array( 'id' => 'admin', 'name' => 'Admin', 'url' => 'admin' );
    }
    $pmenuBlocks[] = array( 'id' => 'about', 'name' => 'About', 'url' => 'about.php' );
    $pmenuBlocks[] = array( 'id' => 'logout', 'name' => 'Logout', 'url' => 'logout.php' );

    $primaryMenuHtml = '';
    $pmenuBlockHtml = '';
    foreach( $pmenuBlocks as $pmenuItem ) {
        $selectedClass = ( $pmenuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
        $fixedUrl = BLOB_WEB_PAGE_TO_ROOT.$pmenuItem['url'];
        $pmenuBlockHtml .= "<li onclick=\"window.location='{$fixedUrl}'\" class=\"{$selectedClass}\"><a href=\"{$fixedUrl}\">{$pmenuItem['name']}</a></li>";
    }
    $primaryMenuHtml .= "<ul>{$pmenuBlockHtml}</ul>";

    blobDatabaseConnect();
    $blob_loggedin_user = blobCurrentUser() ? blobCurrentUser() : "Open User!";
    $user_fullname = blobGetUserFullName( $blob_loggedin_user );
    $avatarURL = getAvatar( $blob_loggedin_user );
    $quote = getQuote();
    $homepage = BLOB_WEB_PAGE_TO_ROOT . 'index.php';
    $profilepage = BLOB_WEB_PAGE_TO_ROOT . 'profile';

    $messagesHtml = messagesPopAllToHtml();
    if( $messagesHtml ) {
        $messagesHtml = "<div class=\"body_padded\">{$messagesHtml}</div>";
    }

    // Send Headers + main HTML code
    Header( 'Cache-Control: no-cache, must-revalidate');		// HTTP/1.1
    Header( 'Content-Type: text/html;charset=utf-8' );		// TODO- proper XHTML headers...
    Header( "Expires: Tue, 23 Jun 2009 12:00:00 GMT");		// Date in the past

    echo "
        <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html xmlns=\"http://www.w3.org/1999/xhtml\">
        <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
        <title>{$pPage['title']}</title>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"".BLOB_WEB_PAGE_TO_ROOT."blob/css/login.css\" />
        <link rel=\"stylesheet\" type=\"text/css\" href=\"".BLOB_WEB_PAGE_TO_ROOT."blob/css/main.css\" />
        <link rel=\"stylesheet\" type=\"text/css\" href=\"".BLOB_WEB_PAGE_TO_ROOT."blob/css/table.css\" />
        <link rel=\"icon\" type=\"\image/ico\" href=\"".BLOB_WEB_PAGE_TO_ROOT."favicon.ico\" />
        <script type=\"text/javascript\" src=\"".BLOB_WEB_PAGE_TO_ROOT."blob/js/blobPage.js\"></script>
<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></script>
{$pPage['script']}
</head>

<body {$pPage['onload']} class=\"home\">
<div id=\"header\">
<a href=\"{$homepage}\"><img class=\"header_img\" src=\"".BLOB_WEB_PAGE_TO_ROOT."blob/images/logo.png\" alt=\"blob\" /></a>
<div id=\"quote\">
    {$quote}
    </div>

    <div id=\"primary_menu\">
    {$primaryMenuHtml}
    </div>
    </div>
    <div id=\"wrapper\">
    <div id=\"container\" class=\"rounded-corners\">
    <div id=\"main_menu\">
    <div id=\"profile_info\">
    <a href=\"{$profilepage}\"><img class=\"rounded-corners\" width=\"100\" src=\"{$avatarURL}\" /></a>
    <div>{$user_fullname}</div>
    <div>{$adminLink}</div>
    </div>
    <div id=\"main_menu_padded\">
    {$menuHtml}
    </div>
    </div>
    <div id=\"main_body\" class=\"rounded-corners\">
    {$pPage['body']}
    <br />
    <center>
    {$messagesHtml}
    </center>
    </div>
    <div class=\"clear\">
    </div>
    </div>
    <div id=\"footer\" class=\"rounded-corners\">
    <p>BLOB v".blobVersionGet()." is a Free and OpenSource Microblogging client</p>
    </div>
    </body>
    </html>";
}

// To be used on all external links --
function blobExternalLinkUrlGet( $pLink,$text=null ) {
    if (is_null($text)){
        return '<a href="'.$pLink.'" target="_blank">'.$pLink.'</a>';
    } else {
        return '<a href="'.$pLink.'" target="_blank">'.$text.'</a>';
    }
}
// -- END

// To be used on all internal links (opens in same page)--
function blobInternalLinkUrlGet( $pLink,$text=null ) {
    if (is_null($text)){
        return '<a href="'.$pLink.'">'.$pLink.'</a>';
    } else {
        return '<a href="'.$pLink.'">'.$text.'</a>';
    }
}
// -- END

// Database Management --
if ($DBMS == 'MySQL') {
    $DBMS = htmlspecialchars(strip_tags($DBMS));
    $DBMS_errorFunc = 'mysql_error()';
}
else {
    $DBMS = "No DBMS selected.";
    $DBMS_errorFunc = '';
}
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

function blobNoLoginHtmlEcho( $pPage, $right ) {
    $homepage = BLOB_WEB_PAGE_TO_ROOT . 'index.php';

    $pmenuBlocks = array();
    $pmenuBlocks[] = array( 'id' => 'login', 'name' => 'Login', 'url' => 'login.php' );
    $pmenuBlocks[] = array( 'id' => 'join', 'name' => 'Join', 'url' => 'join.php' );
    $pmenuBlocks[] = array( 'id' => 'about', 'name' => 'About', 'url' => 'about.php' );
    $primaryMenuHtml = '';
    $pmenuBlockHtml = '';
    foreach( $pmenuBlocks as $pmenuItem ) {
        $selectedClass = ( $pmenuItem[ 'id' ] == $pPage[ 'page_id' ] ) ? 'selected' : '';
        $fixedUrl = BLOB_WEB_PAGE_TO_ROOT.$pmenuItem['url'];
        $pmenuBlockHtml .= "<li onclick=\"window.location='{$fixedUrl}'\" class=\"{$selectedClass}\"><a href=\"{$fixedUrl}\">{$pmenuItem['name']}</a></li>";
    }
    $primaryMenuHtml .= "<ul>{$pmenuBlockHtml}</ul>";

    $quote = getQuote();

    $messagesHtml = messagesPopAllToHtml();
    if( $messagesHtml ) {
        $messagesHtml = "<div class=\"body_padded\">{$messagesHtml}</div>";
    }

    // Send Headers + main HTML code
    Header( 'Cache-Control: no-cache, must-revalidate');		// HTTP/1.1
    Header( 'Content-Type: text/html;charset=utf-8' );		// TODO- proper XHTML headers...
    Header( "Expires: Tue, 23 Jun 2009 12:00:00 GMT");		// Date in the past



    echo "
        <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html xmlns=\"http://www.w3.org/1999/xhtml\">
        <head>
        <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
        <title>{$pPage['title']}</title>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"".BLOB_WEB_PAGE_TO_ROOT."blob/css/login.css\" />
        <link rel=\"stylesheet\" type=\"text/css\" href=\"".BLOB_WEB_PAGE_TO_ROOT."blob/css/main.css\" />
        <link rel=\"icon\" type=\"\image/ico\" href=\"".BLOB_WEB_PAGE_TO_ROOT."favicon.ico\" />
    {$pPage['script']}
    </head>
    <body {$pPage['onload']} class=\"home\">
    <div id=\"header\">
    <a href=\"{$homepage}\"><img class=\"header_img\" src=\"".BLOB_WEB_PAGE_TO_ROOT."blob/images/logo.png\" alt=\"blob\" /></a>
    <div id=\"quote\">
    {$quote}
    </div>

    <div id=\"primary_menu\">
    {$primaryMenuHtml}
    </div>
    </div>
    <div id=\"wrapper\">
    <div id=\"container\" class=\"rounded-corners\">
    <div id=\"main_menu\">
    <div id=\"main_menu_padded\">
    {$right}
    </div>
    </div>
    <div id=\"main_body\" class=\"rounded-corners-left\">
    {$pPage['body']}
    <center>
    {$messagesHtml}
    </center>
    </div>
    <div class=\"clear\">
    </div>
    </div>
    <div id=\"footer\" class=\"rounded-corners\">
    <p>BLOB v".blobVersionGet()." is a Free and OpenSource Microblogging client</p>
    </div>
    </div>
    </body>
    </html>";
}

?>
