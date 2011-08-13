<?php

$version = get_option('acf_version','1.0.5');


require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


/*---------------------------------------------------------------------------------------------
 * Update to 1.1.0 - this version needs tables!
 *
 * @author Elliot Condon
 * @since 1.0.6
 * 
 ---------------------------------------------------------------------------------------------*/
if(version_compare($version,'1.1.0') < 0)
{
	// Version is less than 1.1.0
	
	global $wpdb;
	
	
	// set charset
	if ($wpdb->has_cap('collation'))
	{
		if(!empty($wpdb->charset))
		{
			$charset_collate = " DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate))
		{
			$charset_collate .= " COLLATE $wpdb->collate";
	
		}
	}


	// create acf_fields table
	$table_name = $wpdb->prefix.'acf_fields';
	if(!$wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "CREATE TABLE " . $table_name . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			order_no int(9) NOT NULL DEFAULT '0',
			post_id bigint(20) NOT NULL DEFAULT '0',
			parent_id bigint(20) NOT NULL DEFAULT '0',
			label text NOT NULL,
			name text NOT NULL,
			type text NOT NULL,
			options text NOT NULL,
			UNIQUE KEY id (id)
		) ".$charset_collate.";";
		dbDelta($sql);
	}
	
	
	// create acf_options table
	$table_name = $wpdb->prefix.'acf_options';
	if(!$wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "CREATE TABLE " . $table_name . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			acf_id bigint(20) NOT NULL DEFAULT '0',
			name text NOT NULL,
			value text NOT NULL,
			type text NOT NULL,
			UNIQUE KEY id (id)
		) ".$charset_collate.";";
		dbDelta($sql);
	}
	
	
	// create acf_options table
	$table_name = $wpdb->prefix.'acf_values';
	if(!$wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "CREATE TABLE " . $table_name . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			order_no int(9) NOT NULL DEFAULT '0',
			field_id bigint(20) NOT NULL DEFAULT '0',
			value text NOT NULL,
			post_id bigint(20) NOT NULL DEFAULT '0',
			UNIQUE KEY id (id)
		) ".$charset_collate.";";
		dbDelta($sql);
	}


	
	$acfs = get_posts(array(
		'numberposts' 	=> 	-1,
		'post_type'		=>	'acf',
	));
	
	
	if($acfs)
	{
		foreach($acfs as $acf)
		{
			$keys = get_post_custom_keys($acf->ID);
			
			if(empty($keys)){continue;}
			
				
			// FIELDS
			$table_name = $wpdb->prefix.'acf_fields';
			
			
		 	for($i = 0; $i < 99; $i++)
			{
				if(in_array('_acf_field_'.$i.'_label',$keys))
				{
					$field = array(
						'label'		=>	get_post_meta($acf->ID, '_acf_field_'.$i.'_label', true),
						'name'		=>	get_post_meta($acf->ID, '_acf_field_'.$i.'_name', true),
						'type'		=>	get_post_meta($acf->ID, '_acf_field_'.$i.'_type', true),
						'options'	=> 	unserialize(get_post_meta($acf->ID, '_acf_field_'.$i.'_options', true)) // explode choices!
					);
					
					// if choices, exlode them
					if($field['options']['choices'])
					{
						// explode choices from each line
						if(strpos($field['options']['choices'], "\n") !== false)
						{
							// found multiple lines, explode it
							$field['options']['choices'] = explode("\n", $field['options']['choices']);
						}
						else
						{
							// no multiple lines! 
							$field['options']['choices'] = array($field['options']['choices']);
						}
						
						$new_choices = array();
						foreach($field['options']['choices'] as $choice)
						{
							$new_choices[trim($choice)] = trim($choice);
						}
						
						
						// return array containing all choices
						$field['options']['choices'] = $new_choices;
						
					}
					
					// now save field to database
					$data = array(
						'order_no' 	=> 	$i,
						'post_id'	=>	$acf->ID,
						'label'		=>	$field['label'],
						'name'		=>	$field['name'],
						'type'		=>	$field['type'],
						'options'	=>	serialize($field['options']),
						
					);
					
					
					// save field as row in database
					
					$new_id = $wpdb->insert($table_name, $data);
				}
				else
				{
					// data doesnt exist, break loop
					break;
				}
			}
			
			
			// START LOCATION
			$table_name = $wpdb->prefix.'acf_options';
			//$wpdb->query("DELETE FROM $table_name WHERE acf_id = '$acf->ID' AND type = 'location'");
			
			$location = array(
				'post_types'			=>	get_post_meta($acf->ID, '_acf_location_post_type', true),	
				'page_slugs'			=>	get_post_meta($acf->ID, '_acf_location_page_slug', true),
				'post_ids'			=>	get_post_meta($acf->ID, '_acf_location_post_id', true),
				'page_templates'		=>	get_post_meta($acf->ID, '_acf_location_page_template', true),
				'parent_ids'			=>	get_post_meta($acf->ID, '_acf_location_parent_id', true),
				'ignore_other_acfs'	=>	get_post_meta($acf->ID, '_acf_location_ignore_other_acf', true),
			);
			
			foreach($location as $key => $value)
			{
				if(empty($value))
				{
					continue;
				}
				
				if(strpos($value, ',') !== false)
				{
					// found ',', explode it
					$value = str_replace(', ',',',$value);
					$value = explode(',', $value);
				}
				else
				{
					// no ','! 
					$value = array($value);
				}
	
				
				$new_id = $wpdb->insert($table_name, array(
					'acf_id'	=>	$acf->ID,
					'name'		=>	$key,
					'value'		=>	serialize($value),
					'type'		=>	'location'
				));
			}
			// END LOCATION
			
			
			// START OPTIONS
			$table_name = $wpdb->prefix.'acf_options';
			//$wpdb->query("DELETE FROM $table_name WHERE acf_id = '$acf->ID' AND type = 'option'");
			
			
		 	$show_on_page = get_post_meta($acf->ID, '_acf_option_show_on_page', true);
		 	
		 	
		 	if(!empty($show_on_page))
			{
				$show_on_page = str_replace(', ',',',$show_on_page);
				$show_on_page = explode(',',$show_on_page);
				
				
				$new_id = $wpdb->insert($table_name, array(
					'acf_id'	=>	$acf->ID,
					'name'		=>	'show_on_page',
					'value'		=>	serialize($show_on_page),
					'type'		=>	'option'
				));
			}
			// END OPTIONS
			
			
			// delete data
			foreach(get_post_custom($acf->ID) as $key => $values)
			{
				if(strpos($key, '_acf') !== false)
				{
					// this custom field needs to be deleted!
					delete_post_meta($acf->ID, $key);
				}
			}
		}
	}
	// START VALUES
	
	$table_name = $wpdb->prefix.'acf_values';
	//$wpdb->query("DELETE FROM $table_name WHERE acf_id = '$acf->ID' AND type = 'option'");
	
	$posts = get_posts(array(
		'numberposts'	=>	-1,
		'post_type'		=>	'any'	
	));
	
	if($posts)
	{
		foreach($posts as $post)
		{
			foreach(get_post_custom($post->ID) as $key => $value)
			{
				if(strpos($key, '_acf') !== false)
				{
					// found an acf cusomt field!
					$name = str_replace('_acf_','',$key);
					
					if($name == 'id'){continue;}
					
					// get field id
					$table_name = $wpdb->prefix.'acf_fields';
					$field_id = $wpdb->get_var("SELECT id FROM $table_name WHERE name = '$name'");
					
					$table_name = $wpdb->prefix.'acf_values';
					$new_id = $wpdb->insert($table_name, array(
						'field_id'	=>	$field_id,
						'value'		=>	$value[0],
						'post_id'	=>	$post->ID,
					));
					
				}
			}
			
			// delete data
			foreach(get_post_custom($post->ID) as $key => $values)
			{
				if(strpos($key, '_acf') !== false)
				{
					// this custom field needs to be deleted!
					delete_post_meta($post->ID, $key);
				}
			}
			
	
		}
	}
	
	// END VALUES
	
	
	update_option('acf_version','1.1.0');
	$version = '1.1.0';
}



/*---------------------------------------------------------------------------------------------
 * Update to 1.1.0 - this version adds updates tables to be utf-8
 *
 * @author Elliot Condon
 * @since 1.0.6
 * 
 ---------------------------------------------------------------------------------------------*/
 
if(version_compare($version,'1.1.4') < 0)
{
	// Version is less than 1.1.4
	
	global $wpdb;
	
	
	// set charset
	if(!empty($wpdb->charset))
	{
		$char = $wpdb->charset;
	}
	else
	{
		$char = "utf8";
	}

	
	
	// alter acf_fields table
	$table_name = $wpdb->prefix.'acf_fields';
	if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "ALTER TABLE $table_name charset=$char;";
		$wpdb->query($sql);
	}
	
	
	// alter acf_options table
	$table_name = $wpdb->prefix.'acf_options';
	if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "ALTER TABLE $table_name charset=$char;";
		$wpdb->query($sql);
	}
	
	
	// alter acf_values table
	$table_name = $wpdb->prefix.'acf_values';
	if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "ALTER TABLE $table_name charset=$char;";
		$wpdb->query($sql);
	}
	
	update_option('acf_version','1.1.4');
	$version = '1.1.4';
}


/*---------------------------------------------------------------------------------------------
 * Update to 2.0.1 - this version adds field description and save as custom field columns to acf_fields
 *
 * @author Elliot Condon
 * @since 2.0.6
 * 
 ---------------------------------------------------------------------------------------------*/
 
if(version_compare($version,'2.0.1') < 0)
{

	global $wpdb;
	
	
	// set charset
	if(!empty($wpdb->charset))
	{
		$char = $wpdb->charset;
	}
	else
	{
		$char = "utf8";
	}


	// create acf_fields table
	$table_name = $wpdb->prefix.'acf_fields';
	if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		$sql = "CREATE TABLE " . $table_name . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			order_no int(9) NOT NULL DEFAULT '0',
			post_id bigint(20) NOT NULL DEFAULT '0',
			parent_id bigint(20) NOT NULL DEFAULT '0',
			label text NOT NULL,
			name text NOT NULL,
			instructions text NOT NULL DEFAULT '',
			save_as_cf int(1) NOT NULL DEFAULT 0,
			type text NOT NULL,
			options text NOT NULL,
			UNIQUE KEY id (id)
		) ".$char.";";
		dbDelta($sql);
	}
	
	update_option('acf_version','2.0.1');
	$version = '2.0.1';
}


/*---------------------------------------------------------------------------------------------
 * Update to 2.0.1 - this version adds field description and save as custom field columns to acf_fields
 *
 * @author Elliot Condon
 * @since 2.0.6
 * 
 ---------------------------------------------------------------------------------------------*/

if(version_compare($version,'2.0.2') < 0)
{
	global $wpdb;
	
	
	// 1. CREATE NEW RULES TABLE
	
	$table_name = $wpdb->prefix.'acf_rules';

	if ($wpdb->has_cap('collation'))
	{
		if(!empty($wpdb->charset))
		{
			$charset_collate = " DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate))
		{
			$charset_collate .= " COLLATE $wpdb->collate";
	
		}
	}
	
	if(!$wpdb->get_var("SHOW TABLES LIKE '".$table_name."'"))
	{
		// rules table does not exist

		$sql = "CREATE TABLE " . $table_name . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			acf_id bigint(20) NOT NULL DEFAULT '0',
			order_no int(9) NOT NULL DEFAULT '0',
			param text NOT NULL,
			operator text NOT NULL,
			value text NOT NULL,
			UNIQUE KEY id (id)
		) ".$charset_collate.";";
		dbDelta($sql);
		
	}
	
	
	// 2. SAVE LOCATION DATA AS RULES
	
	$table_name = $wpdb->prefix.'acf_options';
	$rules = array();
	
	 	
 	// get fields and add them to $options
 	$locations = $wpdb->get_results("SELECT * FROM $table_name WHERE type = 'location'");
 	
 	
 	// rewrite into new format
 	if($locations)
 	{
	 	foreach($locations as $location)
	 	{

	 		$values = unserialize($location->value);
	 		
	 		foreach($values as $value)
	 		{
	 		
	 			$rules[] = array(
	 				'acf_id' 	=>	$location->acf_id,
	 				'order_no'	=>	0,
	 				'param'		=>	substr($location->name, 0, -1),
	 				'operator'	=>	'==',
	 				'value'		=>	$value,
	 			);
	 			
	 		}
	 	}
 	}
 	
 	
 	// 3. SAVE USER TYPE OPTION DATA AS RULES
	
	
 	// get fields and add them to $options
 	$locations = $wpdb->get_results("SELECT * FROM $table_name WHERE type = 'option' AND name = 'user_roles'");
 	
 	
 	// rewrite into new format
 	if($locations)
 	{
 		
 		$user_roles = array(
 			'10'	=>	'administrator',
 			'7'		=>	'editor',
 			'4'		=>	'author',
 			'1'		=>	'contributor',
 		);
 		
 		
	 	foreach($locations as $location)
	 	{

	 		$values = unserialize($location->value);
	 		
	 		foreach($values as $value)
	 		{
	 			
	 			$rules[] = array(
	 				'acf_id' 	=>	$location->acf_id,
	 				'order_no'	=>	0,
	 				'param'		=>	'user_type',
	 				'operator'	=>	'==',
	 				'value'		=>	$user_roles[$value],
	 			);
	 			
	 		}
	 	}
 	}

 	
 	// 4. SAVE RULES INTO DATABASE
 	
 	$table_name = $wpdb->prefix.'acf_rules';
 	
 	foreach($rules as $rule)
 	{
 		$wpdb->insert($table_name, $rule);
 	}
 	
 	
 	// 5. SAVE OPTIONS
 	
 	$table_name = $wpdb->prefix.'acf_options';
	$options = array();
	
 	$options = $wpdb->get_results("SELECT * FROM $table_name WHERE type = 'option' AND name = 'show_on_page'");
 	
 	if($options)
 	{
	 	foreach($options as $option)
	 	{
			update_post_meta($option->acf_id, 'show_on_page', $option->value);
			
	 	}
 	}
 	
 	
 	// 6. SAVE NEW POST META
 	
 	$options = $wpdb->get_results("SELECT DISTINCT acf_id FROM $table_name WHERE type = 'option'");
 	
 	
 	if($options)
 	{
	 	foreach($options as $option)
	 	{
			update_post_meta($option->acf_id, 'allorany', 'any');
			update_post_meta($option->acf_id, 'field_group_layout', 'default');	
	 	}
 	}
 	
 	
 	// drop options table
 	$wpdb->query("DROP TABLE $table_name");
 	
 		
	update_option('acf_version','2.0.2');
	$version = '2.0.2';
		
	
}

update_option('acf_version',$this->version);

?>