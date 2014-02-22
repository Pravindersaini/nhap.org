<?php

add_action('wp_router_generate_routes', 'tribe_set_community_template', 100, 1);

function tribe_set_community_template( $router ) {

	$new_template = array(			

		'template' => array('tribe-events/default-template-community.php'), // edit this filename to be the one you want to use in your theme

	);

	$router->edit_route('ce-edit-venue-route', $new_template);

	$router->edit_route('ce-edit-organizer-route', $new_template);

	$router->edit_route('ce-edit-route', $new_template);

	$router->edit_route('ce-edit-redirect-route', $new_template);

	$router->edit_route('ce-add-route', $new_template);

	$router->edit_route('ce-delete-route', $new_template);

	$router->edit_route('ce-list-route', $new_template);

	$router->edit_route('ce-list-route-args', $new_template);

}

/* remove the admin bar for all non-admin users 
/* and add location for mobile menu
*/
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  //define('TRIBE_DISABLE_TOOLBAR_ITEMS', true);
	  show_admin_bar(false);
	}
	/* now add mobile menu location */
	register_nav_menu ('primary mobile', __( 'Navigation Mobile', 'sahifa-child' ));
}

function set_container_class ($args) {
	$args['container_class'] = str_replace(' ','-',$args['theme_location']).'-nav'; 
	return $args;
}
add_filter ('wp_nav_menu_args', 'set_container_class');

?>