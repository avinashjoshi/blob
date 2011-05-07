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

$page = blobPageNewGrab();

blobDatabaseConnect();
$user = blobCurrentUser();
$user_id = blobGetUserID( $user );

if(isset($_GET['unfollow']))
{
    $page[ 'title' ] .= $page[ 'title_separator' ].'Unfollow User';
    $page[ 'page_id' ] = 'unfollowuser';
    $user = stripslashes($_GET['unfollow']);
    $user = mysql_real_escape_string($user);
    // Check if the user exists
    if ( !blobExistUser($user) ) {
        blobMessagePush( "'".$user."' does not exist!" );
        blobRedirect( 'following.php' );
    }

    $fullName = blobGetUserFullName($user);
    $avatar = getAvatar($user);

    $unFollowHTML = blobUnFollowUser($user);
    $profilepage = BLOB_WEB_PAGE_TO_ROOT . 'profile/view.php?user=' . $user;
    $page[ 'body' ] .= "
        <div class=\"body_padded\">
        <h2>Following User: {$user}</h2>

        <div class=\"vulnerable_code_area\">
        <div style=\"float: left; padding-right: 10px; border-right: 2px solid #C0C0C0;\">
        <img src=\"{$avatar}\" width=\"100\" />
        </div>
        <div style=\"margin-left: 120px;\">
        ".blobInternalLinkUrlGet($profilepage,$fullName)."
        <br /><br />
    {$unFollowHTML}
    <br /><br />
    </div>
    </div>

    <br />
    <b>View user's profile:</b> ".blobInternalLinkUrlGet($profilepage,$fullName)."
    <br /><br /><br />

    </div>
    ";
} else {
    $page[ 'title' ] .= $page[ 'title_separator' ].'You are following...';
    $page[ 'page_id' ] = 'following';
    $page[ 'body' ] .= "
        <div class=\"body_padded\">
        <h2>You are following</h2>

        ".blobFollowUserList()."
        <br /> <br />

        </div>
        ";
}

blobHtmlEcho( $page );
?>
