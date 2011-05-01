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
$page[ 'title' ] .= $page[ 'title_separator' ].'View Profile';
$page[ 'page_id' ] = 'viewprofile';

blobDatabaseConnect();
$user = blobCurrentUser();

if(isset($_GET['user']) && $_GET['user'] != $user )
{

$user = $_GET['user'];
//$user = mysql_real_escape_string($user);
// Check if the user exists
if ( !blobExistUser($user) ) {
	blobMessagePush( "'".$user."' does not exist!" );
	blobRedirect( 'view.php' );
}
$fullName = blobGetUserFullName($user);
$avatar = getAvatar($user);

$followHTML = blobCanFollowHTML($user);


if (blobCanFollow($user))
	$showStatusHTML = "<div id=\"comments_main\"><div id=\"comments\"><pre width=\"77\">You will be able to see his updates only if you follow the user!</pre> </div></div>";
else
	$showStatusHTML = blobShowUserStatus($user);

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h2>User Profile: {$user}</h2>

	<div class=\"vulnerable_code_area\">
		<div style=\"float: left; padding-right: 10px; border-right: 2px solid #C0C0C0;\">
			<img src=\"{$avatar}\" width=\"100\" />
		</div>
		<div style=\"margin-left: 120px;\">
			{$fullName}
			<br /><br />
			{$followHTML}
		</div>
	</div>

	<div class=\"clear\"></div>
	<pre>User's status updates:</pre>
	{$showStatusHTML}
	<br /><br /><br />

</div>
";
} else {

$user_id = blobGetUserID( $user );
$fullName = blobGetUserFullName($user);
$avatar = getAvatar($user);
$showStatusHTML = blobShowUserStatus($user);
$profileUrl = BLOB_WEB_PAGE_TO_ROOT;
$user = $user . " (that's me!)";

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h2>User Profile: {$user}</h2>

	<div class=\"vulnerable_code_area\">
		<div style=\"float: left; padding-right: 10px; border-right: 2px solid #C0C0C0;\">
			<img src=\"{$avatar}\" width=\"100\" />
		</div>
		<div style=\"margin-left: 120px;\">
			{$fullName}
			<br /><br />
			<input class=\"button\" name=\"btnUpdate\" type=\"submit\" value=\"Update your status\" onclick=\"window.location='{$profileUrl}'\">
		</div>
	</div>

	<div class=\"clear\"></div>
	<pre>Your previous status updates:</pre>
	{$showStatusHTML}
	<br /><br /><br />

</div>
";
}

blobHtmlEcho( $page );
?>