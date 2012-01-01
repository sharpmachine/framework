<?php
function remove_dashboard_widgets(){
  global$wp_meta_boxes;
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); 
  unset($wp_meta_boxes['dashboard']['normal']['core']['yoast_db_widget']);
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function modify_footer_admin () {
  echo 'Created by <a href="http://sharpmachinemedia.com">Sharp Machine Media</a>.';
  echo '  Powered by <a href="http://WordPress.org">WordPress</a>.';
}

add_filter('admin_footer_text', 'modify_footer_admin');

//Custom logo should be 20 x 20
function custom_logo() {
  echo '<style type="text/css">
    #header-logo  { 
    	background-image: url('.get_bloginfo('template_directory').'/images/admin-logo.png) !important; 
	}
	#cpt_info_box {
		display: none !important; /* Hides Custom Post Type info box */
	}
    </style>';
}

add_action('admin_head', 'custom_logo');

//Login Logo
function custom_login_logo() {
  echo '<style type="text/css">
    h1 a { background-image:url('.get_bloginfo('template_directory').'/images/login-logo.png) !important; }
    </style>';
}

add_action('login_head', 'custom_login_logo');

// Remove items from admin menu bar
// function remove_admin_bar_links() {
// 	global $wp_admin_bar;
// 	$wp_admin_bar->remove_menu('themes');
// }
// add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

// Add items to admin menu bar
// function my_admin_bar_link() {
// 	global $wp_admin_bar;
// 	if ( !is_super_admin() || !is_admin_bar_showing() )
// 		return;
// 	$wp_admin_bar->add_menu( array(
// 	'id' => 'new_link',
// 	'parent' => 'new-content',
// 	'title' => __( 'Link'),
// 	'href' => admin_url( 'link-add.php' )
// 	) );
// }
// add_action('admin_bar_menu', 'my_admin_bar_link');
?>