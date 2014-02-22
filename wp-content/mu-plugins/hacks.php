<?php	
	include_once('includes/gender.php');
	include_once('includes/postcode/class.Postcode.php');
	include_once('includes/cameo.php');
	include_once('includes/stripattributes.php');
	
	
	/* Change default Wordpress emails addresses used for notifications to users etc. */
	add_filter('wp_mail_from', 'new_mail_from');
	add_filter('wp_mail_from_name', 'new_mail_from_name');

	function new_mail_from($old) {
	 return 'no-reply@nhap.org';
	}
	function new_mail_from_name($old) {
	 return 'NHA Party';
	}
	
	/* DONATIONS URL HACK - CHANGE TO GO LIVE */
	/*
	/*---*/
	/*
	function change_paypal_url( $url ) {
	  $url='https://www.sandbox.paypal.com/cgi-bin/webscr?';
	  return $url;
	}
	add_filter('paypal_donations_url', 'change_paypal_url', 10, 1);	
	*/

	/*	
	add_action('after_setup_theme', 'remove_admin_bar');
	function remove_admin_bar() {
		if (!current_user_can('administrator') && !is_admin()) {
		  show_admin_bar(false);
		}
	}
	*/	
	
	/******************/
	/* S2Member Hacks */
	/******************/
	
	// Need to add 'custom' and 'item_number' fields to IPN messages for legacy subscriptions
	add_filter('ws_plugin__s2member_paypal_postvars', 'my_postvars_filter');
	function my_postvars_filter($postvars = array())
	{
		if(!array_key_exists('custom', $postvars))
			$postvars['custom'] = $_SERVER['HTTP_HOST'];
		if(!array_key_exists('item_number', $postvars))
			$postvars['item_number'] = 1;			
		return $postvars;
	}

	// Send reminder email if subscription fails
	//add_action('ws_plugin__s2member_during_paypal_notify_after_subscr_failed', 'send_subscr_failed_email');
	function send_subscr_failed_email($vars) {
		$buyer_email = "Buyer email";
		$fname = "First name";
		if(isset($vars['paypal']['payer_email']))
			$buyer_email = $vars['paypal']['payer_email'];
		if(isset($vars['paypal']['first_name']))
			$fname = $vars['paypal']['first_name'];
		file_put_contents('/home/national/logs/debug.log', print_r($buyer_email, true), FILE_APPEND);
		file_put_contents('/home/national/logs/debug.log', print_r($fname, true), FILE_APPEND);
		file_put_contents('/home/national/logs/debug.log', print_r($vars, true), FILE_APPEND);
	}
	



	// Set double opt-in email from mailchimp for Level 0, otherwise no double opt-in required
	add_filter("ws_plugin__s2member_mailchimp_double_optin", "double_optin", 10, 2);
	function double_optin($double_opt_in, $vars) {
		$optIn = false;
		$level = $vars["level"];
		if ($level == 0) {
			//$optIn = true;
			$optIn = false;
		} else {
			$optIn = false;
		}
		return $optIn;
	}
	
	// Turn on welcome email from mailchimp
	add_filter("ws_plugin__s2member_mailchimp_send_welcome", "__return_true");
	
	// Turn on update existing user option in mailchimp
	add_filter("ws_plugin__s2member_mailchimp_update_existing", "__return_true");
	
	// add extra info in mailchimp merge fields
	add_filter("ws_plugin__s2member_mailchimp_merge_array", "merge_filter", 10, 2);
	function merge_filter($merge, $vars) {
		// $merge /* Array of existing MERGE fields that s2Member passes by default. */
		// $vars /* Array of defined variables in the scope/context of this Filter. */
		// process date of birth field
		$my_dob_merge_vars = array();
		$user_id = $vars["user_id"];
		$dob = get_user_field("dob", $user_id);
		if ("" != $dob) {
			$dob = date_parse_from_format('j/n/Y', $dob);
			if ($dob[warning_count] == 0) {
				$birthday = $dob['month'].'/'.$dob['day'];
				$dob = date('d M Y', mktime(0,0,0,$dob['month'], $dob['day'], $dob['year']));
				$my_dob_merge_vars = array("DOB" => $dob, "BIRTHDAY" => $birthday);
			}
		}
		// lookup postcode related data
		$postcode = get_user_field("postcode", $user_id);		
		$my_postcode_merge_vars = get_postcode_data($postcode, false);
		
		// guess gender from first name
		$strictness = 9; // Set this to 1 or 2 for more restrictive matching
		$firstname = $vars['fname'];
		$result = gender($firstname, $strictness);
		if (isset($result))	{
			$gender = $result['gender'];
			if ($gender === 'f') {
				$my_gender_merge_vars = array ("GENDER" => 'Female');
			} else {
				$my_gender_merge_vars = array ("GENDER" => 'Male');
			}
		} else {
			$my_gender_merge_vars = array ("GENDER" => 'Unknown');     
		}
		return array_merge_recursive($merge, $my_dob_merge_vars, $my_postcode_merge_vars, $my_gender_merge_vars);
	}
	
	/* one off function to resend welcome email to all users */
	/*
	//add_action('init', 'reset_pass_resend_new_user_notifications');
	
	function reset_pass_resend_new_user_notifications()
    {
        if(!empty($_GET[__FUNCTION__]) && $_GET[__FUNCTION__] === 'my-secret-password')
            {
				//$users = get_users('include=2490,2859');
				$users = get_users('exclude=2861,2856,1514,1516,2040,1609,1732,1783,1925,1935,2086,2044,2146,2163,2175,2245,2273,2456,2480,2614,2664,1766');
				foreach($users as $user) {
                    //file_put_contents('/home/national/logs/debug.log', print_r(get_user_field ("user_email", $user->ID) . ",", true), FILE_APPEND);
					file_put_contents('/home/national/logs/debug.log', print_r($user->ID . ",", true), FILE_APPEND);
					c_ws_plugin__s2member_email_configs::reset_pass_resend_new_user_notification($user->ID);
					}
				exit('All done!');
			}

            
    }
	*/
	
	/* This routine was used to force WP users into MC
	//add_action("init", "sync_mc", 1);
	function sync_mc(){
		$args = array
		(
		'blog_id'      => $GLOBALS['blog_id'],
		'role'         => '',
		'meta_key'     => '',
		'meta_value'   => '',
		'meta_compare' => '',
		'meta_query'   => array(),
		'include'      => array(),
		'exclude'      => array(),
		'orderby'      => 'login',
		'order'        => 'ASC',
		'offset'       => '',
		'search'       => '',
		'number'       => '',
		'count_total'  => false,
		'fields'       => 'all',
		'who'          => ''
		);
		$users = get_users('search=terencenorman@mac.com');
		foreach ($users as $user) {
			$role = get_user_field ("s2member_access_role", $user->ID);
			$level = get_user_field ("s2member_access_level", $user->ID);
			$login = get_user_field ("user_login", $user->ID);
			$pass = "xxx";
			$email = get_user_field ("user_email", $user->ID);
			$fname = get_user_field ("first_name", $user->ID);
			$lname = get_user_field ("last_name", $user->ID);
			$ip = get_user_field ("s2member_registration_ip", $user->ID);
			$opt_in = true;
			$user_id = $user->ID;
			c_ws_plugin__s2member_list_servers::process_list_servers ($role, $level, $login, $pass, $email, $fname, $lname, $ip, $opt_in, true, $user_id);
			file_put_contents('/home/national/logs/debug.log', print_r($email, true));
		}

		return;
	}
	*/	
	
	// Postie hacks
	function postie_strip_tags($post) {
		$text = $post['post_content'];
		$text = strip_word_html($text);
		$sa = new StripAttributes();
		$sa->allow = array('href', 'target');
		$text = $sa->strip($text);
		$pattern = '/<a /';
		$replace = "<a target='_blank' ";  // force target='_blank' for any <a> tags
		$text = preg_replace($pattern, $replace, $text);

		//file_put_contents('/home/national/logs/debug.log', print_r($text, true), FILE_APPEND);
		$post['post_content'] = $text;
		return $post;
	}
	
	add_filter('postie_post_before', 'postie_strip_tags');
	
	function strip_word_html($text, $allowed_tags = '<a><b><i><sup><sub><em><strong><u><br>') 
    { 
        mb_regex_encoding('UTF-8'); 
        //replace MS special characters first 
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u'); 
        $replace = array('\'', '\'', '"', '"', '-'); 
        $text = preg_replace($search, $replace, $text); 
        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears 
        //in some MS headers, some html entities are encoded and some aren't 
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8'); 
        //try to strip out any C style comments first, since these, embedded in html comments, seem to 
        //prevent strip_tags from removing html comments (MS Word introduced combination) 
        if(mb_stripos($text, '/*') !== FALSE){ 
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm'); 
        } 
        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be 
        //'<1' becomes '< 1'(note: somewhat application specific) 
        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text); 
        $text = strip_tags($text, $allowed_tags); 
        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one 
        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text); 
        //strip out inline css and simplify style tags 
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu'); 
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>'); 
        $text = preg_replace($search, $replace, $text); 
        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears 
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains 
        //some MS Style Definitions - this last bit gets rid of any leftover comments */ 
        $num_matches = preg_match_all("/\<!--/u", $text, $matches); 
        if($num_matches){ 
              $text = preg_replace('/\<!--(.)*--\>/isu', '', $text); 
        } 
        return $text; 
    }
			
	// Mailchimp for Wordpress Hack
	add_filter('mc4wp_merge_vars', 'add_postcode_data', 10, 1);
	function add_postcode_data($merge_vars) 	{
		//file_put_contents('/home/national/logs/debug.log', print_r($merge_vars, true));
		$postcode_merge_vars = array();
		if(isset($merge_vars["POSTCODE"])) {
			$postcode_merge_vars = get_postcode_data($merge_vars["POSTCODE"], true);
		}
		$postcode_merge_vars['GROUPINGS'][] = $merge_vars['GROUPINGS'][0];
		//file_put_contents('/home/national/logs/debug.log', print_r(array_merge($merge_vars, $postcode_merge_vars), true));
		return array_merge($merge_vars, $postcode_merge_vars);
	}	
	/* Other Hacks */
	// change 'Howdy' to 'Hello'
	add_action( 'admin_bar_menu', 'wp_admin_bar_my_custom_account_menu', 11 );
	function wp_admin_bar_my_custom_account_menu( $wp_admin_bar ) {
	$user_id = get_current_user_id();
	$current_user = wp_get_current_user();
	$profile_url = get_edit_profile_url( $user_id );
		if ( 0 != $user_id ) {
		/* Add the "My Account" menu */
		$avatar = get_avatar( $user_id, 28 );
		$howdy = sprintf( __('Hello, %1$s'), $current_user->display_name );
		$class = empty( $avatar ) ? '' : 'with-avatar';
		$wp_admin_bar->add_menu( array(
		'id' => 'my-account',
		'parent' => 'top-secondary',
		'title' => $howdy . $avatar,
		'href' => $profile_url,
		'meta' => array(
		'class' => $class,
		),
		) );
		}
	}
	function get_postcode_data($postcode, $mcapi2=true) {
		if (Postcode::isValidFormat($postcode)) {
			$postcode = Postcode::getUnit($postcode);		
			$postArea 	= Postcode::getArea($postcode);
			$postDistrict = Postcode::getDistrict($postcode);
			// get data from MapIt api
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://mapit.mysociety.org/postcode/". urlencode($postcode)); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$mapitData = curl_exec($ch);
			if(200 == curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
				$mapitData = json_decode($mapitData, true);
				$wmcCode = $mapitData['shortcuts']['WMC'];
				$wardCode = $mapitData['shortcuts']['ward'];
				$councilCode = $mapitData['shortcuts']['council'];
				// need to search $mapitData for EUR area as no shortcode is available
				$regionAreaData = array_filter($mapitData['areas'], function($ar) {return ($ar['type'] == 'EUR');});
				$regionAreaData = array_shift($regionAreaData);
				// debug code for future reference
				// file_put_contents('/home/national/logs/debug.log', print_r($regionAreaData, true));
				$region = $regionAreaData['name'];
				$constituency = $mapitData['areas'][(string)$wmcCode]['name'];
				$ward = $mapitData['areas'][(string)$wardCode]['name'];
				$council = $mapitData['areas'][(string)$councilCode]['name'];
			}
			$cameocode = cameoCode($postcode);
		}
		
		if($mcapi2 == true) {
			$groups = array(
							array('name'=>'Region', 'groups'=>array($region)),
							array('name'=>'Send me', 'groups'=>array('Weekly Updates', 'Newsletters'))
							);
		} else {
			$groups = array(
							array('name'=>'Region', 'groups'=>$region),
							array('name'=>'Send me', 'groups'=>'Weekly Updates, Newsletters')
							);
		}
		$postcode_data = array	(							
								'GROUPINGS'=>$groups,																									
								"POSTCODE" => $postcode,
								"POSTAREA" => $postArea,
								"DISTRICT" => $postDistrict,
								"REGION" => $region,
								"CONSTNAME" => $constituency,
								"WARD" => $ward,
								"COUNCIL" => $council,
								"CAMEOCODE" => $cameocode,
								);
		return $postcode_data;
	}
	
	/* WP to Twitter hacks */
	/* Schedule tweets to target peak hours */
	add_filter( 'wpt_schedule_retweet', 'my_retweet_schedule', 10, 4 );
	function my_retweet_schedule( $time, $acct, $rt, $post ) {
		$t1 = 9;
		$t2 = 13;
		$t3 = 19;
		$hour = 60 * 60;
		$min = 0; // 15 minutes before
		$max = 900; // 15 minutes after
		$now = getdate();
		$rt1 = $t1 - $now['hours'];
		//file_put_contents('/home/national/logs/debug.log', print_r($rt1, true), FILE_APPEND);
		if($rt1 <= 0)
			$rt1 += 24;
		$rt2 = $t2 - $now['hours'];
		if($rt2 <= 0)
			$rt2 += 24;
		$rt3 = $t3 - $now['hours'];
		if($rt3 <= 0)
			$rt3 += 24;
		$rnd_delay = mt_rand(0, 900);
		file_put_contents('/home/national/logs/debug.log', print_r($rnd_delay, true), FILE_APPEND);
		$rt1 = ($rt1 * $hour) + $rnd_delay - ($now['minutes']*60);
		$rt2 = ($rt2 * $hour) + mt_rand( $min, $max ) - ($now['minutes']*60);
		$rt3 = ($rt3 * $hour) + mt_rand( $min, $max ) - ($now['minutes']*60);
		$delays = array($rt1, $rt2, $rt3);
		//file_put_contents('/home/national/logs/debug.log', print_r($rt1, true), FILE_APPEND);
		sort($delays);
		switch ( $rt ) {
			  case 1: return $delays[0]; break;
			  case 2: return $delays[1]; break;
			  case 3: return $delays[2]; break;
		}
		return $time;
	}	
	
	function category_list() {
		wp_list_categories('orderby=name');
		return;
	}
	add_shortcode('category_list', 'category_list');
	
	function donate_btn() {
		$markup = '<a class="call-to-action red" title="Donate" href="http://nhap.org/donate/">DONATE TODAY</a>';
		return $markup;
	}
	add_shortcode('donate', 'donate_btn');
	function connect_btn() {
		$markup = '<a class="call-to-action red" title="Get Connected" href="http://nhap.org/connect/">GET CONNECTED</a>';
		return $markup;
	}
	add_shortcode('connect', 'connect_btn');	
	function join_btn() {
		$markup = '<a class="call-to-action red" title="Join Now" href="http://nhap.org/join/">JOIN NOW</a>';
		return $markup;
	}
	add_shortcode('join', 'join_btn');		
	function buzz_btn() {
		$markup = '<a class="call-to-action red" title="The BUZZ" href="http://nhap.org/buzz/">GET THE BUZZ</a>';
		return $markup;
	}
	add_shortcode('buzz', 'buzz_btn');	
	function events_btn() {
		$markup = '<a class="call-to-action blue" href="http://nhap.org/events/" title="EVENTS"><div class="icon events">EVENTS</div></a>';
		return $markup;
	}
	add_shortcode('events', 'events_btn');	
	function localgroups_btn() {
		$markup = '<a class="call-to-action blue" href="http://nhap.org/local-groups-page/" title="LOCAL GROUPS"><div class="icon localgroups">LOCAL GROUPS</div></a>';
		return $markup;
	}
	add_shortcode('groups', 'localgroups_btn');	
	function volunteer_btn() {
		$markup = '<a class="call-to-action blue" href="http://nhap.org/volunteering-page/" title="VOLUNTEER"><div class="icon volunteer">VOLUNTEER</div></a>';
		return $markup;
	}
	add_shortcode('volunteer', 'volunteer_btn');
	function memberzone_btn() {
		$markup = '<a class="call-to-action blue" href="http://nhap.org/member-zone-page/" title="MEMBER ZONE"><div class="icon memberzone">MEMBER ZONE</div></a>';
		return $markup;
	}
	add_shortcode('memberzone', 'memberzone_btn');
	
?>