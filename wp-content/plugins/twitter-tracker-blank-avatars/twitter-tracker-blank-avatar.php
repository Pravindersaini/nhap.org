<?php

/*
Plugin Name: Twitter Tracker: Blank Avatars
Plugin URI: http://wordpress.org/extend/plugins/twitter-tracker-blank-avatars/
Description: Blank avatars for Twitter Tracker.
Version: 1.0
Author: Code for the People Ltd
Author URI: http://codeforthepeople.com/
*/
 
/*  Copyright 2012 Code for the People Ltd

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

/**
 * Hooks the WP tt_avatar_bigger_url and tt_avatar_url filters
 * to probide blank avatars for Twitter Tracker.
 *
 * @param string $avatar_url The URL for the Twitter Avatar 
 * @return string The URL for the WP blank GIF image
 **/
function ttba_tt_avatar_url( $avatar_url ) {
	return home_url( '/wp-includes/images/blank.gif' );
}
add_filter( 'tt_avatar_url', 'ttba_tt_avatar_url' );
add_filter( 'tt_avatar_bigger_url', 'ttba_tt_avatar_url' );

?>