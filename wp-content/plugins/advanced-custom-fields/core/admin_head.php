<?php

global $post;



/*----------------------------------------------------------------------
*
*	deactivate_field
*
*---------------------------------------------------------------------*/

if(isset($_POST['acf_field_deactivate']))
{
	// delete field
	$field = $_POST['acf_field_deactivate'];
	$option = 'acf_'.$field.'_ac';
	delete_option($option);
	
	
	// update activated fields
	$this->activated_fields = $this->get_activated_fields();
	$this->fields = $this->_get_field_types();
	
	
	//set message
	$acf_message_field = "";
	if($field == "repeater")
	{
		$acf_message_field = "Repeater Field";
	}
	elseif($field == "options_page")
	{
		$acf_message_field = "Options Page";
	}
	
	
	// show message on page
	$this->admin_message($acf_message_field.' deactivated');
	
}



/*----------------------------------------------------------------------
*
*	activate_field
*
*---------------------------------------------------------------------*/

if(isset($_POST['acf_field_activate']) && isset($_POST['acf_ac']))
{
	
	$field = $_POST['acf_field_activate'];
	$ac = $_POST['acf_ac'];
	
	
	// update option
	$option = 'acf_'.$field.'_ac';
	update_option($option, $ac);
	
	
	// update activated fields
	$old_count = count($this->activated_fields);
	$this->activated_fields = $this->get_activated_fields();
	$this->fields = $this->_get_field_types();
	$new_count = count($this->activated_fields);
	
	
	// set message
	global $acf_message_field;
	$acf_message_field = "";
	if($field == "repeater")
	{
		$acf_message_field = "Repeater Field";
	}
	elseif($field == "options_page")
	{
		$acf_message_field = "Options Page";
	}
	
	
	// show message
	if($new_count == $old_count)
	{
		$this->admin_message('Activation code unrecognized');
	}
	else
	{
		$this->admin_message($acf_message_field.' activated');
	}
	
	
	
}



/*----------------------------------------------------------------------
*
*	Options
*
*---------------------------------------------------------------------*/

if(!array_key_exists('options_page', $this->activated_fields))
{
	?>
	<style type="text/css">
		#adminmenu li.menu-top#toplevel_page_acf-options {
			display: none;
		}
	</style>
	<?php
}


// get current page
$currentFile = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentFile);
$currentFile = $parts[count($parts) - 1];


// only add html to post.php and post-new.php pages
if($currentFile == 'post.php' || $currentFile == 'post-new.php')
{
	
	if(get_post_type($post) == 'acf')
	{
	
		// ACF 
		echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.fields.js" ></script>';
		echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.location.js" ></script>';
		
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.global.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.fields.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.location.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.options.css" />';
		
		add_meta_box('acf_fields', 'Fields', array($this, '_fields_meta_box'), 'acf', 'normal', 'high');
		add_meta_box('acf_location', 'Location </span><span class="description">- Add Fields to Edit Screens', array($this, '_location_meta_box'), 'acf', 'normal', 'high');
		add_meta_box('acf_options', 'Advanced Options</span><span class="description">- Customise the edit page', array($this, '_options_meta_box'), 'acf', 'normal', 'high');
	
	}
	else
	{
		// any other edit page
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' 	=>	'menu_order',
		));
		
		// blank array to hold acfs
		$add_acf = array();
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				$add_box = false;
				$location = $this->get_acf_location($acf->ID);
				
				
				if($location->allorany == 'all')
				{
					// ALL
					
					$add_box = true;
					
					if($location->rules)
					{
						foreach($location->rules as $rule)
						{
							// if any rules dont return true, dont add this acf
							if(!$this->match_location_rule($post, $rule))
							{
								$add_box = false;
							}
						}
					}
					
				}
				elseif($location->allorany == 'any')
				{
					// ANY
					
					$add_box = false;
					
					if($location->rules)
					{
						foreach($location->rules as $rule)
						{
							// if any rules return true, add this acf
							if($this->match_location_rule($post, $rule))
							{
								$add_box = true;
							}
						}
					}
				}
							
				if($add_box == true)
				{
					$add_acf[] = $acf;
				}
				
			}// end foreach
			
			if(!empty($add_acf))
			{
			
				// create tyn mce instance for wysiwyg
				$post_type = get_post_type($post);
				if(!post_type_supports($post_type, 'editor'))
				{
					wp_tiny_mce();
				}


				// add these acf's to the page
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.global.css" />';
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.input.css" />';
				echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.input.js" ></script>';
				
				
				// date picker!
				echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/core/fields/date_picker/style.date_picker.css" />';
				echo '<script type="text/javascript" src="'.$this->dir.'/core/fields/date_picker/jquery.ui.datepicker.js" ></script>';
					
				add_meta_box('acf_input', 'ACF Fields', array($this, '_input_meta_box'), $post_type, 'normal', 'high', array('acfs' => $add_acf));
			}
			
		}// end if
	}
}


?>