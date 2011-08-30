<?php

/*--------------------------------------------------------------------------------------
*
*	Integrate with Duplicate Posts plugin
*
*	@author unknownnf - thanks mate
*	@since 2.0.6
* 
*-------------------------------------------------------------------------------------*/
function acf_duplicate($newId, $post)
{

	// tables
	global $wpdb;
	$acf_values = $wpdb->prefix.'acf_values';
	$wp_postmeta = $wpdb->prefix.'postmeta';
	
	
	// get rows
	$sql = "SELECT m.meta_key, m.meta_value, v.value, v.field_id, v.sub_field_id, v.order_no 
		FROM $wp_postmeta m LEFT JOIN $acf_values v ON m.meta_id = v.value 
		WHERE m.post_id = '$post->ID'";
		
	$rows = $wpdb->get_results($sql);
	
	foreach ($rows as $row) {
		
		// save postmeta
		$data = array(
			'post_id' => $newId, 
			'meta_key' => $row->meta_key,
			'meta_value' => $row->meta_value,
		);
		
		$wpdb->insert($wp_postmeta, $data);
		
		$new_value_id = $wpdb->insert_id;
		
		if($new_value_id && $new_value_id != 0)
		{
			// create data object to save
			$data2 = array(
				'post_id' => $newId, 
				'order_no' => $row->order_no,
				'field_id' => $row->field_id,
				'sub_field_id' => $row->sub_field_id,
				'value' => $new_value_id,
			);
			
			$wpdb->insert($acf_values, $data2);
		}

	}

}

add_action('dp_duplicate_page', 'acf_duplicate', 10, 2);
add_action('dp_duplicate_post', 'acf_duplicate', 10, 2); 

?>