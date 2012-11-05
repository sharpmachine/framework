<?php

/*-----------------------------------------------------------------------------------*/
/*	Scripts
/*-----------------------------------------------------------------------------------*/

if (!function_exists('function sf_scripts')) {
    function sf_scripts(){
        wp_register_script('sf_scripts', get_template_directory_uri() . '/script.js', array('jquery'), null, true );
        wp_enqueue_script('sf_scripts');
    }
}
add_action('wp_enqueue_scripts', 'sf_scripts');

/*-----------------------------------------------------------------------------------*/
/* Check for the ACF Plugin
/*-----------------------------------------------------------------------------------*/

if (!function_exists('sf_acf_check')) {
    function sf_acf_check() {
    	if(!function_exists('register_field_group')) {
    		add_thickbox(); // Required for the plugin install dialog.
    		add_action( 'admin_notices', 'sf_acf_check_notice' );
    	}
    }
}
add_action('admin_init', 'sf_acf_check');

if (!function_exists('sf_acf_check_notice')) {
    function sf_acf_check_notice() {
    	if(!current_user_can('install_plugins')) return;
    ?>	
    	<div class="updated fade">
    		<p>The Advanced Custom Fields plugin is required in order for the So Simple theme to function properly. <a href="<?php echo admin_url('plugin-install.php?tab=plugin-information&plugin=advanced-custom-fields&TB_iframe=true&width=640'); ?>" class="thickbox onclick">Click Here</a> to install and activate this required plugin.</p>
    	</div>
    
    <?php
    }
}

/*-----------------------------------------------------------------------------------*/
/*	Hide Unused Admin Items Please
/*-----------------------------------------------------------------------------------*/

if (!function_exists('sst_hide_menus')) {
    function sst_hide_menus() {
    	global $current_user;
    	get_currentuserinfo();
    
    	if($current_user->user_login = 'admin') {
    		?>
    		<style>
    		#menu-posts,
    		#menu-media,
    		#menu-links,
    		#menu-pages,
    		#menu-comments,
    		.wp-menu-separator,
    		#wp-admin-bar-new-content,
    		#wp-admin-bar-comments,
    		#toplevel_page_edit-post_type-acf {
    		display:none;
    		}
    		</style>
    		<?php
    	}
    }
}
add_action('admin_head', 'sst_hide_menus');

/*-----------------------------------------------------------------------------------*/
/* Advanced Custom Fields
/*-----------------------------------------------------------------------------------*/

if(!get_option('acf_repeater_ac')) update_option('acf_repeater_ac', "QJF7-L4IX-UCNP-RF2W");
if(!get_option('acf_options_page_ac')) update_option('acf_options_page_ac', "OPN8-FA4J-Y2LW-81LS");
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => '5084a809ecc15',
		'title' => 'Intro Text Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_507f27bd1be93',
				'label' => 'Enable Intro Text',
				'name' => 'sf_enable_intro_text',
				'type' => 'true_false',
				'instructions' => 'Check the box below to enable the intro text.',
				'required' => '0',
				'message' => 'Enable Intro Text',
				'order_no' => '0',
			),
			1 => 
			array (
				'key' => 'field_507e2f24e4bc8',
				'label' => 'Intro Title Text',
				'name' => 'sf_intro_title_text',
				'type' => 'text',
				'instructions' => 'Enter the text that you would like to use for the title.',
				'required' => '0',
				'default_value' => 'We Are Getting Ready to Launch',
				'formatting' => 'none',
				'order_no' => '1',
			),
			2 => 
			array (
				'key' => 'field_507e2f24e4eb3',
				'label' => 'Intro Paragraph Text',
				'name' => 'sf_intro_paragraph_text',
				'type' => 'wysiwyg',
				'instructions' => 'Enter the text you wish to use for your intro paragraph.',
				'required' => '0',
				'default_value' => 'We are getting ready to launch and you could be the first in line to be notified. Just enter your email address below to subscribe to our email newsletter.',
				'toolbar' => 'basic',
				'media_upload' => 'no',
				'the_content' => 'no',
				'order_no' => '2',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => '5084a809ed332',
		'title' => 'Style Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_507e2e163a41d',
				'label' => 'Background Photo',
				'name' => 'sf_background_photo',
				'type' => 'image',
				'instructions' => 'If you would like to use a full-screen background photo as shown in the demo, upload one here.',
				'required' => '0',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'order_no' => '0',
			),
			1 => 
			array (
				'choices' => 
				array (
					'bg-white' => 'White',
					'bg-blue' => 'Blue',
					'bg-red' => 'Red',
					'bg-green' => 'Green',
					'bg-black' => 'Black',
				),
				'key' => 'field_507e2fd1304c7',
				'label' => 'Background Color',
				'name' => 'sf_background_color',
				'type' => 'select',
				'instructions' => 'If you\'re not using a background photo or image (above), select the color you would like to use for your background.',
				'required' => '0',
				'default_value' => 'bg-white : White',
				'allow_null' => '0',
				'multiple' => '0',
				'order_no' => '1',
			),
			2 => 
			array (
				'choices' => 
				array (
					'white' => 'White',
					'black' => 'Black',
				),
				'key' => 'field_50844d7b492fe',
				'label' => 'Intro Text Color',
				'name' => 'sf_intro_text_color',
				'type' => 'select',
				'instructions' => 'Select the text color that is going to look best with your background color or image.',
				'required' => '0',
				'default_value' => 'black : Black',
				'allow_null' => '0',
				'multiple' => '0',
				'order_no' => '2',
			),
			3 => 
			array (
				'choices' => 
				array (
					'white' => 'White',
					'black' => 'Black',
				),
				'key' => 'field_50844e86b7348',
				'label' => 'Panel Nav Text Color',
				'name' => 'sf_panel_nav_text_color',
				'type' => 'select',
				'instructions' => 'Select the text color you would like to use for the panel navigation menu (bottom).',
				'required' => '0',
				'default_value' => 'black : Black',
				'allow_null' => '0',
				'multiple' => '0',
				'order_no' => '3',
			),
			4 => 
			array (
				'key' => 'field_50844d7b4964d',
				'label' => 'Text Translucency',
				'name' => 'sf_text_translucency',
				'type' => 'true_false',
				'instructions' => 'Check the box below if you would like to add translucency to your text.',
				'required' => '0',
				'message' => 'Add Text Translucency',
				'order_no' => '4',
			),
			5 => 
			array (
				'key' => 'field_50844d7b49b26',
				'label' => 'Text Shadow',
				'name' => 'sf_text_shadow',
				'type' => 'true_false',
				'instructions' => 'Check the box below if you would like to add a drop shadow to your text.',
				'required' => '0',
				'message' => 'Add Text Shadow',
				'order_no' => '5',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => '5084a809edbf3',
		'title' => 'Logo & Favicon Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_507e340a1542f',
				'label' => 'Logo Image',
				'name' => 'sf_logo_image',
				'type' => 'image',
				'instructions' => 'Upload your logo image to replace the default logo image.',
				'required' => '0',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'order_no' => '0',
			),
			1 => 
			array (
				'key' => 'field_50809567ec654',
				'label' => 'Favicon Image',
				'name' => 'sf_favicon_image',
				'type' => 'image',
				'instructions' => 'Upload your favicon image to replace the default favicon (no larger than 32 x 32 for the best results).',
				'required' => '0',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'order_no' => '1',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 1,
	));
	register_field_group(array (
		'id' => '5084a809ee06c',
		'title' => 'MailChimp Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_507ef91b0d213',
				'label' => 'Enable MailChimp',
				'name' => 'sf_enable_mailchimp',
				'type' => 'true_false',
				'instructions' => 'Check the box below to enable the MailChimp subscription form.',
				'required' => '0',
				'message' => 'Enable MailChimp',
				'order_no' => '0',
			),
			1 => 
			array (
				'key' => 'field_507ef91b0cb34',
				'label' => 'MailChimp API Key',
				'name' => 'sf_mailchimp_api_key',
				'type' => 'text',
				'instructions' => 'Enter your MailChimp API Key below. You will find your API key by clicking the “Account” tab within your MailChimp account.',
				'required' => '0',
				'default_value' => '',
				'formatting' => 'none',
				'order_no' => '1',
			),
			2 => 
			array (
				'key' => 'field_507ef91b0cf61',
				'label' => 'MailChimp List ID',
				'name' => 'sf_mailchimp_list_id',
				'type' => 'text',
				'instructions' => 'Enter your MailChimp List ID below. You will find your List ID by clicking “List Settings & Unique ID” for any MailChimp list.',
				'required' => '0',
				'default_value' => '',
				'formatting' => 'none',
				'order_no' => '2',
			),
			3 => 
			array (
				'key' => 'field_507f26d95f159',
				'label' => 'MailChimp Success Message',
				'name' => 'sf_mailchimp_success_message',
				'type' => 'text',
				'instructions' => 'Enter the message you would like to display on a successful subscription.',
				'required' => '0',
				'default_value' => 'Thanks for signing up!',
				'formatting' => 'none',
				'order_no' => '3',
			),
			4 => 
			array (
				'key' => 'field_5080900015d4a',
				'label' => 'MailChimp Placeholder Text',
				'name' => 'sf_mailchimp_placeholder_text',
				'type' => 'text',
				'instructions' => 'Enter the text you would like to use for the email field placeholder text.',
				'required' => '0',
				'default_value' => 'enter your email address then hit enter to subscribe',
				'formatting' => 'none',
				'order_no' => '4',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'any',
		),
		'options' => 
		array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 1,
	));
	register_field_group(array (
		'id' => '5084a809ee724',
		'title' => 'Social Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_507e31815c41c',
				'label' => 'Twitter',
				'name' => 'sf_twitter_id',
				'type' => 'text',
				'instructions' => 'Enter your Twitter ID if you wish to enable a link to your Twitter account.',
				'required' => '0',
				'default_value' => '',
				'formatting' => 'none',
				'order_no' => '0',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 2,
	));
	register_field_group(array (
		'id' => '5084a809ee9ec',
		'title' => 'Analytics Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_5084a7bba2b5e',
				'label' => 'Analytics Tracking Code',
				'name' => 'sf_analytics_tracking_code',
				'type' => 'textarea',
				'instructions' => 'Paste your Google or other analytics tracking code below to enable site analytics.',
				'required' => '0',
				'default_value' => '',
				'formatting' => 'none',
				'order_no' => '0',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 3,
	));
	register_field_group(array (
		'id' => '5084a809eec8f',
		'title' => 'Text Panel Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_507ef31793065',
				'label' => 'Enable Text Panels',
				'name' => 'sf_enable_text_panels',
				'type' => 'true_false',
				'instructions' => 'Check the box below if you would like to enable text panels.',
				'required' => '0',
				'message' => 'Enable Text Panels',
				'order_no' => '0',
			),
			1 => 
			array (
				'key' => 'field_507e36a6889d9',
				'label' => 'Text Panels',
				'name' => 'sf_text_panels',
				'type' => 'repeater',
				'instructions' => 'Add up to 3 text panels (pop-up panels) using the options below.',
				'required' => '0',
				'sub_fields' => 
				array (
					0 => 
					array (
						'key' => 'field_507e36a688a01',
						'label' => 'Panel Title',
						'name' => 'sf_panel_title',
						'type' => 'text',
						'instructions' => 'The panel title (e.g. “About”)',
						'column_width' => '25',
						'default_value' => '',
						'formatting' => 'none',
						'order_no' => '0',
					),
					1 => 
					array (
						'key' => 'field_507e36a688a1a',
						'label' => 'Panel Text',
						'name' => 'sf_panel_text',
						'type' => 'wysiwyg',
						'instructions' => 'The text you would like to be displayed within the pop-up panel.',
						'column_width' => '75',
						'default_value' => '',
						'toolbar' => 'basic',
						'media_upload' => 'no',
						'the_content' => 'no',
						'order_no' => '1',
					),
				),
				'row_min' => '1',
				'row_limit' => '3',
				'layout' => 'row',
				'button_label' => 'Add Panel',
				'order_no' => '1',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 3,
	));
	register_field_group(array (
		'id' => '5084a809ef06d',
		'title' => 'Profiles Panel Options',
		'fields' => 
		array (
			0 => 
			array (
				'key' => 'field_50837afe3feb6',
				'label' => 'Enable the Profiles Panel',
				'name' => 'sf_enable_profiles_panel',
				'type' => 'true_false',
				'instructions' => 'Check the box below to enable the profiles panel.',
				'required' => '0',
				'message' => 'Enable Profiles Panel',
				'order_no' => '0',
			),
			1 => 
			array (
				'key' => 'field_507ef1047c4e8',
				'label' => 'Profiles Panel Name',
				'name' => 'sf_profiles_panel_name',
				'type' => 'text',
				'instructions' => 'What\'s the name you would like to use for your profiles panel? (e.g. “Team”)',
				'required' => '0',
				'default_value' => '',
				'formatting' => 'none',
				'order_no' => '1',
			),
			2 => 
			array (
				'key' => 'field_507ef1047c7fe',
				'label' => 'Profiles',
				'name' => 'sf_profiles',
				'type' => 'repeater',
				'instructions' => 'Add up to 6 profile images, links and names. ',
				'required' => '0',
				'sub_fields' => 
				array (
					0 => 
					array (
						'key' => 'field_507ef1047c814',
						'label' => 'Profile Name',
						'name' => 'sf_profile_name',
						'type' => 'text',
						'instructions' => 'e.g. “Joe” or “Jane”',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'none',
						'order_no' => '0',
					),
					1 => 
					array (
						'key' => 'field_507ef1047c827',
						'label' => 'Profile Link',
						'name' => 'sf_profile_link',
						'type' => 'text',
						'instructions' => 'This profile links to?',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'none',
						'order_no' => '1',
					),
					2 => 
					array (
						'key' => 'field_507ef1047c833',
						'label' => 'Profile Image',
						'name' => 'sf_profile_image',
						'type' => 'image',
						'instructions' => 'Upload an image for this profile.',
						'column_width' => '',
						'save_format' => 'url',
						'preview_size' => 'thumbnail',
						'order_no' => '2',
					),
				),
				'row_min' => '1',
				'row_limit' => '6',
				'layout' => 'row',
				'button_label' => 'Add Profile',
				'order_no' => '2',
			),
		),
		'location' => 
		array (
			'rules' => 
			array (
				0 => 
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'Options',
					'order_no' => '0',
				),
			),
			'allorany' => 'all',
		),
		'options' => 
		array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => 
			array (
			),
		),
		'menu_order' => 4,
	));
}

/*-----------------------------------------------------------------------------------*/
/* WP App Store Integration
/*-----------------------------------------------------------------------------------*/

include("wp-app-store.php");

?>