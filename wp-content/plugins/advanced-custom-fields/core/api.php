<?php

global $acf_global;

$acf_global = array(
	'field_id'	=>	0,
	'post_id'	=>	0,
	'order_no'	=>	-1,
);
		
/*--------------------------------------------------------------------------------------
*
*	get_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_field($field_name, $post_id = false)
{

	global $post;
	global $wpdb;
	global $acf;
	
	
	// tables
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_fields = $wpdb->prefix.'acf_fields';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 0;
	}
	
	
	$sql = "SELECT m.meta_value as value, f.type, f.options 
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value
		LEFT JOIN $acf_fields f ON v.field_id = f.id 
		WHERE f.name = '$field_name' AND m.post_id = '$post_id'";
		
	$results = $wpdb->get_results($sql);
	
	
	// no value
	if(!$results)
	{
		return false;
	}
	
	
	// repeater field
	if(count($results) > 1)
	{
		return true;
	}
	
	
	// normal field
	$field = $results[0];
	$value = $field->value;
	
	
	// format if needed
 	if($acf->field_method_exists($field->type, 'format_value_for_api'))
	{
		
		if(@unserialize($field->options))
		{
			$field->options = unserialize($field->options);
		}
		else
		{
			$field->options = array();
		}
		
		$value = $acf->fields[$field->type]->format_value_for_api($value, $field->options);
	}
	
	return $value;
	
}


/*--------------------------------------------------------------------------------------
*
*	the_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_field($field_name, $post_id = false)
{

	$value = get_field($field_name, $post_id);
	
	if(is_array($value))
	{
		$value = @implode(', ',$value);
	}
	
	echo $value;
		
}


/*--------------------------------------------------------------------------------------
*
*	the_repeater_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_repeater_field($field_name, $post_id = false)
{

	global $acf_global;
	global $post;
	global $wpdb;
	global $acf;
	
	
	// tables
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_fields = $wpdb->prefix.'acf_fields';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 0;
	}
	
	
	// vars
	$acf_global['order_no']++;
	$order_no = $acf_global['order_no'];
	
	
	$sql = "SELECT v.field_id 
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value
		LEFT JOIN $acf_fields f ON v.field_id = f.id 
		WHERE f.name = '$field_name' AND v.order_no = '$order_no' AND m.post_id = '$post_id'";
		
	$results = $wpdb->get_results($sql);
	
	
	// no value
	if($results)
	{
		
		$acf_global['field_id'] = $results[0]->field_id;
		$acf_global['post_id'] = $post_id;	
		return true;
	}
	else
	{
		$acf_global['field_id'] = 0;
		$acf_global['post_id'] = 0;
		$acf_global['order_no'] = -1;
		return false;
	}
	
	
		
}


/*--------------------------------------------------------------------------------------
*
*	get_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_sub_field($field_name)
{
	global $acf_global;
	global $wpdb;
	global $acf;
	
	
	// tables
	$acf_values = $wpdb->prefix.'acf_values';
	$acf_fields = $wpdb->prefix.'acf_fields';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	// vars
	$field_id = $acf_global['field_id'];
	$post_id = $acf_global['post_id'];
	$order_no = $acf_global['order_no'];
		
		
	$sql = "SELECT m.meta_value as value, f.type, f.options 
		FROM $wp_postmeta m 
		LEFT JOIN $acf_values v ON m.meta_id = v.value
		LEFT JOIN $acf_fields f ON v.sub_field_id = f.id 
		WHERE f.name = '$field_name' AND v.field_id = '$field_id' AND v.order_no = '$order_no' AND m.post_id = '$post_id'";
		
	$field = $wpdb->get_row($sql);
	
	
	// no value
	if(!$field)
	{
		return false;
	}


	// normal field
	$value = $field->value;
	
	
	// format if needed
 	if($acf->field_method_exists($field->type, 'format_value_for_api'))
	{
		
		if(@unserialize($field->options))
		{
			$field->options = unserialize($field->options);
		}
		else
		{
			$field->options = array();
		}
		
		$value = $acf->fields[$field->type]->format_value_for_api($value, $field->options);
	}
	
	return $value;
}


/*--------------------------------------------------------------------------------------
*
*	the_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_sub_field($field_name, $field = false)
{
	$value = get_sub_field($field_name, $field);
	
	if(is_array($value))
	{
		$value = implode(', ',$value);
	}
	
	echo $value;
}

?>