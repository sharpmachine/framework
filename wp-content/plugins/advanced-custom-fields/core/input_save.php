<?php
/*---------------------------------------------------------------------------------------------
	Fields Meta Box
---------------------------------------------------------------------------------------------*/
if(isset($_POST['input_meta_box']) && $_POST['input_meta_box'] == 'true')
{

    // If acf was not posted, don't go any further
    if(!isset($_POST['acf']))
    {
    	return true;
    }
    
    
    // set table name
	global $wpdb;
	$table_name = $wpdb->prefix.'acf_values';
	
	// remove all old values from the database
	$wpdb->query("DELETE FROM $table_name WHERE post_id = '$post_id'");
		
    foreach($_POST['acf'] as $field)
    {	
    	if(method_exists($this->fields[$field['field_type']], 'save_input'))
		{
			$this->fields[$field['field_type']]->save_input($post_id, $field);
		}
		else
		{
			//$field = apply_filters('wp_insert_post_data', $field);
			$field = stripslashes_deep( $field );
			
			
			// if select is a multiple (multiple select value), you need to save it as an array!
			if(is_array($field['value']))
			{
				$field['value'] = serialize($field['value']);
			}
			
			
			// create data object to save
			$data = array(
				'post_id'	=>	$post_id,
				'field_id'	=>	$field['field_id'],
				'value'		=>	$field['value']
			);
			
			// if there is an id, this value already exists, so save it in the same ID spot
			if($field['value_id'])
			{
				$data['id']	= $field['value_id'];
			}
			
			
			// insert new data
			$new_id = $wpdb->insert($table_name, $data);
		}
		
		
		
		// save as standard cf
		if(isset($field['save_as_cf']))
		{
			if(is_array($field['value']))
			{
				$field['value'] = serialize($field['value']);
			}
			
			update_post_meta($post_id, $field['save_as_cf'], $field['value']);
		}
    	
		
    }
  
	
}

?>