<?php
/*

Plugin Name: bbPress Stay Checked
Plugin URI: http://www.trottyzone.com/bbpress-stay-checked/
Description: Checks Checkbox by default in bbPress Forum
Version: 1.0
Author: Ephrain Marchan
Author URI: http://www.trottyzone.com
License: OpenSource under GPL2
*/

/*

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/


function subscribed_by_default() {

echo '<script type="text/javascript">jQuery("#bbp_topic_subscription").prop("checked","checked");</script>';
}
add_action('bbp_theme_after_topic_form_subscriptions', 'subscribed_by_default');
add_action('bbp_theme_after_reply_form_subscription', 'subscribed_by_default');

?>