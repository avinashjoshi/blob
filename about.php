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
$page[ 'title' ] .= $page[ 'title_separator' ].'About';
$page[ 'page_id' ] = 'about';

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>About</h1>

<pre>
Version ".blobVersionGet()." (Release date: ".blobReleaseDateGet().")

blob is a micro-blogging service where you can share notices
about yourself with friends, family, and colleagues!

Copyright (C) 2011  Avinash Joshi <avinashtjoshi@gmail.com>

blob is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

blob is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
</pre>
<p>
	<h2>Links</h2>

	<ul>
		<li>Web site: ".blobExternalLinkUrlGet( 'http://avinashjoshi.co.in/' )."</li>
		<li>Download site: ".blobExternalLinkUrlGet( 'http://bitbucket.org/avinashjoshi/blob/downloads' )."</li>
		<li>Mercurial Repo: <pre>hg clone https://avinashjoshi@bitbucket.org/avinashjoshi/blob</pre></li>
	</ul>
</p>

</div>
";

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

if (blobIsLoggedIn())
  blobHtmlEcho( $page );
else
  blobNoLoginHtmlEcho( $page, $right );
?>
