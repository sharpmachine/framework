<?php

class acf_Repeater
{
	var $name;
	var $title;
	var $parent;
	
	
	function acf_Repeater($parent)
	{
		$this->name = 'repeater';
		$this->title = __("Repeater",'acf');
		$this->parent = $parent;
	}
	
	
	function html($field)
	{
		$sub_fields = $field->options['sub_fields'];
		
		$row_limit = 999;
		if(isset($field->options['row_limit']) && $field->options['row_limit'] != "")
		{
			$row_limit = intval($field->options['row_limit']);
		}
		
		$layout = 'table';
		if(isset($field->options['layout']) && $field->options['layout'] != "")
		{
			$layout = $field->options['layout'];
		}
		
		
		
		
		?>
		<div class="repeater">
			<input type="hidden" name="row_limit" value="<?php echo $row_limit; ?>" />
			<table class="widefat <?php if($layout == 'row'): ?>row_layout<?php endif; ?>">
			<?php if($layout == 'table'): ?>
			<thead>
				<tr>
					<?php if($row_limit > 1): ?>
					<th class="order"><!-- order --></th>
					<?php endif; ?>
					
					<?php foreach($sub_fields as $sub_field):?>
					<th class="<?php echo $sub_field->name; ?>" style="width:<?php echo 100/count($sub_fields); ?>%;"><span><?php echo $sub_field->label; ?></span></th>
					<?php endforeach; ?>
					
					<?php if($row_limit > 1): ?>
					<th class="remove"></th>
					<?php endif; ?>
				</tr>
			</thead>
			<?php endif; ?>
			<tbody>
				<?php foreach($field->value as $i => $value):?>
				<?php if(($i+1) > $row_limit){continue;} ?>
				<tr>
					
					<?php if($row_limit > 1): ?>
						<td class="order">
						<?php echo $i+1; ?>
						</td>
					<?php endif; ?>
					
					<?php if($layout == 'row'): ?><td><?php endif; ?>
					
					
					<?php foreach($sub_fields as $j => $sub_field):?>
					
					<?php if($layout == 'table'): ?>
					<td>
					<?php else: ?>
					<label><?php echo $sub_fields[$j]->label; ?></label>
					<?php endif; ?>	
					
						<input type="hidden" name="<?php echo $field->input_name.'['.$i.']['.$j.'][row_id]'; ?>" value="<?php echo $field->value[$i][$j]->id; ?>" />
						<input type="hidden" name="<?php echo $field->input_name.'['.$i.']['.$j.'][field_id]'; ?>" value="<?php echo $sub_field->id; ?>" />
						<input type="hidden" name="<?php echo $field->input_name.'['.$i.']['.$j.'][field_type]' ?>" value="<?php echo $sub_field->type; ?>" />
						
						<?php
						$temp_field = new stdClass();
						$temp_field->type = $sub_field->type;
						$temp_field->input_name = $field->input_name.'['.$i.']['.$j.'][value]';
						$temp_field->input_class = $sub_field->type;
						$temp_field->options = $sub_field->options;
						$temp_field->value = $field->value[$i][$j]->value;
						$this->parent->create_field($temp_field); 
						
						?>
					<?php if($layout == 'table'): ?>
					</td>
					<?php else: ?>

					<?php endif; ?>	
					
					<?php endforeach; ?>
					
					<?php if($layout == 'row'): ?></td><?php endif; ?>
					
					<?php if($row_limit > 1): ?>
						<td class="remove"><a class="remove_field" href="javascript:;"></a></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
			</table>
			<?php if($row_limit > 1): ?>
			<div class="table_footer">
				<div class="order_message"></div>
				<a href="javascript:;" id="add_field" class="button-primary">+ Add Field</a>
			</div>	
			<?php endif; ?>	
		</div>
		<?php
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * Options HTML
	 * - called from fields_meta_box.php
	 * - displays options in html format
	 *
	 * @author Elliot Condon
	 * @since 1.1
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function options_html($key, $options)
	{
		if(isset($options['sub_fields']))
		{
			$fields = $options['sub_fields'];
		}
		else
		{
			$fields = array();
		}
		

		

		// add clone
		$field = new stdClass();
		$field->label = 'New Field';
		$field->name = 'new_field';
		$field->type = 'text';
		$field->options = array();
		$fields[999] = $field;

		
		// get name of all fields for use in field type
		$fields_names = array();
		foreach($this->parent->fields as $field)
		{
			$fields_names[$field->name] = $field->title;
		}
		unset($fields_names['repeater']);
		
		?>
<tr class="field_option field_option_repeater">
<td class="label">
	<label for=""><?php _e("Repeater Fields",'acf'); ?></label>
</td>
<td>
<div class="repeater">
	<table class="acf widefat">
		<thead>
			<tr>
				<th class="field_order"><?php _e('Field Order','acf'); ?></th>
				<th class="field_label"><?php _e('Field Label','acf'); ?></th>
				<th class="field_name"><?php _e('Field Name','acf'); ?></th>
				<th class="field_type"><?php _e('Field Type','acf'); ?></th>
			</tr>
		</thead>
	</table>
	<div class="fields">
	
		<div class="no_fields_message" <?php if(sizeof($fields) > 1){ echo 'style="display:none;"'; } ?>>
			No fields. Click the "Add Field" button to create your first field.
		</div>

		
		<?php foreach($fields as $key2 => $field): ?>
			<div class="<?php if($key2 == 999){echo "field_clone";}else{echo "field";} ?> sub_field">
				
				<input type="hidden" name="acf[fields][<?php echo $key; ?>][sub_fields][<?php echo $key2; ?>][id]'" value="<?php echo $field->id; ?>" />
				<table class="acf widefat">
					<tr>
						<td class="field_order"><?php echo ($key2+1); ?></td>
						<td class="field_label">
							<strong>
								<a class="acf_edit_field" title="Edit this Field" href="javascript:;"><?php echo $field->label; ?></a>
							</strong>
							<div class="row_options">
								<span><a class="acf_edit_field" title="Edit this Field" href="javascript:;">Edit</a> | </span>
								<span><a class="acf_delete_field" title="Delete this Field" href="javascript:;">Delete</a>
							</div>
						</td>
						<td class="field_name"><?php echo $field->name; ?></td>
						<td class="field_type"><?php echo $field->type; ?></td>
					</tr>
				</table>
				
				<div class="field_form_mask">
				<div class="field_form">
					
					<table class="acf_input widefat">
						<tbody>
							<tr class="field_label">
								<td class="label">
									<label><span class="required">*</span>Field Label</label>
									<p class="description">This is the name which will appear on the EDIT page</p>
								</td>
								<td>
									<?php 
									$temp_field = new stdClass();
									
									$temp_field->type = 'text';
									$temp_field->input_name = 'acf[fields]['.$key.'][sub_fields]['.$key2.'][label]';
									$temp_field->input_class = 'label';
									$temp_field->value = $field->label;
									
									$this->parent->create_field($temp_field);
							
									?>
									
								</td>
							</tr>
							<tr class="field_name">
								<td class="label"><label><span class="required">*</span>Field Name</label>
								<p class="description">Single word, no spaces. Underscores and dashes allowed</p>
								</td>
								<td>
									<?php 
								
									$temp_field->type = 'text';
									$temp_field->input_name = 'acf[fields]['.$key.'][sub_fields]['.$key2.'][name]';
									$temp_field->input_class = 'name';
									$temp_field->value = $field->name;
									
									$this->parent->create_field($temp_field);
								
									?>
									
								</td>
							</tr>
							<tr class="field_type">
								<td class="label"><label><span class="required">*</span>Field Type</label></td>
								<td>
									<?php 
								
									$temp_field->type = 'select';
									$temp_field->input_name = 'acf[fields]['.$key.'][sub_fields]['.$key2.'][type]';
									$temp_field->input_class = 'type';
									$temp_field->value = $field->type;
									$temp_field->options = array('choices' => $fields_names);
									
									$this->parent->create_field($temp_field);
								
									?>
									
								</td>
							</tr>
							<?php foreach($fields_names as $field_name => $field_title): ?>
								<?php if(method_exists($this->parent->fields[$field_name], 'options_html')): ?>
			
									<?php $this->parent->fields[$field_name]->options_html($key.'][sub_fields]['.$key2, $field->options); ?>
			
								<?php endif; ?>
							<?php endforeach; ?>
							<tr class="field_save">
								<td class="label"><label>Save Field</label>
									<p class="description">This will save your data and reload the page</p>
								</td>
								<td><input type="submit" value="Save Field" class="button-primary" name="save" />
									or <a class="acf_edit_field" title="Hide this edit screen" href="javascript:;">continue editing ACF</a>
								</td>
								
							</tr>
						</tbody>
					</table>
			
				</div><!-- End Form -->
				</div><!-- End Form Mask -->
			
			</div>
		<?php endforeach; ?>
	</div>
	<div class="table_footer">
		<!-- <div class="order_message"></div> -->
		<a href="javascript:;" id="add_field" class="button-primary">+ Add Field</a>
	</div>
</div>
</td>
</tr>
<tr class="field_option field_option_repeater">
	<td class="label">
		<label for="acf[fields][<?php echo $key; ?>][options][row_limit]"><?php _e("Row Limit",'acf'); ?></label>
	</td>
	<td>
	<input type="text" name="acf[fields][<?php echo $key; ?>][options][row_limit]" id="acf[fields][<?php echo $key; ?>][options][row_limit]" value="<?php echo $options['row_limit']; ?>" />
	
	</td>
</tr>
<tr class="field_option field_option_repeater">
	<td class="label">
		<label for="acf[fields][<?php echo $key; ?>][options][layout]"><?php _e("Layout",'acf'); ?></label>
	</td>
	<td>
		
		<?php 
								
		$temp_field->type = 'select';
		$temp_field->input_name = 'acf[fields]['.$key.'][options][layout]';
		$temp_field->input_class = 'type';
		$temp_field->value = $options['layout'];
		$temp_field->options = array('choices' => array(
			'table'	=>	'Table (default)',
			'row'	=>	'Row'
		));
		
		$this->parent->create_field($temp_field);
	
		?>
	
	</td>
</tr>
<?php
}
	
	
	
	/*---------------------------------------------------------------------------------------------
	 * Save Field
	 * - called from fields_save.php
	 *
	 * @author Elliot Condon
	 * @since 1.1.5
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function save_field($post_id, $parent_id, $field)
	{
		$i = 0;
		
		// set table name
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_fields';
		
		//echo '<pre>';
		//print_r($field);
		//echo '</pre>';
		//die;
		if($field['sub_fields'])
		{
		foreach($field['sub_fields'] as $key => $field)
		{
			if($key == 999)
			{
				continue;
			}
			
			// defaults
			if(!isset($field['label'])) { $field['label'] = ""; }
			if(!isset($field['name'])) { $field['label'] = ""; }
			if(!isset($field['type'])) { $field['label'] = "text"; }
			if(!isset($field['options'])) { $field['options'] = array(); }
			
			
			// format options if needed
			if(method_exists($this->parent->fields[$field['type']], 'format_options'))
			{
				$field['options'] = $this->parent->fields[$field['type']]->format_options($field['options']);
			}
			
			
			// create data
			$data = array(
				'order_no' 	=> 	$i,
				'post_id'	=>	$post_id,
				'parent_id'	=>	$parent_id,
				'label'		=>	$field['label'],
				'name'		=>	$field['name'],
				'type'		=>	$field['type'],
				'options'	=>	serialize($field['options']),
				
			);
			
			
			// options does save. Choices is being overriden by other field options that use the same key name
			// update js to disable all other options
			
			
			// if there is an id, this field already exists, so save it in the same ID spot
			if($field['id'])
			{
				$data['id']	= $field['id'];
			}
			
			
			// save field as row in database
			$new_id = $wpdb->insert($table_name, $data);
			
			
			// increase order_no
			$i++;
		}
		}
		
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * save_input
	 * - called from input_save.php
	 *
	 * @author Elliot Condon
	 * @since 1.1.5
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function save_input($post_id, $field)
	{
		//print_r($field);
		//die;
		
		// set table name
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_values';
		
		
		$field = stripslashes_deep( $field );
		
		
		if($field['value'])
		{
			$i = 0;
			foreach($field['value'] as $row)
			{
				// $i = row number
				foreach($row as $j => $cell)
				{
					
					// if select is a multiple (multiple select value), you need to save it as an array!
					if(isset($cell['value']) && $cell['value'] != "")
					{
						if(is_array($cell['value']))
						{
							$cell['value'] = serialize($cell['value']);
						}
					}
					else
					{
						//$cell['value'] = "";
						continue;
					}

					
					// $j = cell number
					$data = array(
						'post_id'	=>	$post_id,
						'field_id'	=>	$cell['field_id'],
						'value'		=>	$cell['value'],
						'order_no'	=>	$i
					);
					
					
					// if there is an id, this value already exists, so save it in the same ID spot
					if($cell['row_id'])
					{
						$data['id']	= $cell['row_id'];
					}
					
					
					// insert new data
					$new_id = $wpdb->insert($table_name, $data);
					
				}
				//foreach($row as $j => $cell)
				
				$i++;
			}
			//foreach($field['value'] as $i => $row)
		}
		//if($field['value'])
	}
	
	/*---------------------------------------------------------------------------------------------
	 * load_value_for_input
	 * - called from acf.php - load_value_for_input
	 *
	 * @author Elliot Condon
	 * @since 1.1.5
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function load_value_for_input($post_id, $field)
	{
		$sub_fields = $field->options['sub_fields'];
		$values = array();
		
		
		// set table name
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_values';
		
	 	
	 	foreach($sub_fields as $sub_field)
	 	{
	 		// get var
		 	$db_values = $wpdb->get_results("SELECT * FROM $table_name WHERE field_id = '$sub_field->id' AND post_id = '$post_id' ORDER BY order_no ASC");
		 	
		 	if($db_values)
		 	{
		 		foreach($db_values as $db_value)
			 	{
			 		
			 		// format if needed
					if(method_exists($this->parent->fields[$sub_field->type], 'format_value_for_input'))
					{
						$db_value->value = $this->parent->fields[$sub_field->type]->format_value_for_input($db_value->value);
					}
			 		
			 		$values[$db_value->order_no][$sub_field->order_no] = $db_value;
			 	}

		 	}
		 	else
		 	{
		 		// $values[0][$sub_field->name] = "";
		 		$value = new stdClass();
		 		$value->value = "";
		 		$values[0][$sub_field->order_no] = $value;
		 	}
		 			 	
	 	}
	 	
	 	//print_r($values);
	 	return $values;
		
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * load_value_for_api
	 * - called from acf.php - load_value_for_api
	 *
	 * @author Elliot Condon
	 * @since 1.1.5
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function load_value_for_api($post_id, $field)
	{
		//echo '<pre>';
		//print_r($field);
		//echo '</pre>';
		//die;
		
		
		$sub_fields = $field->options['sub_fields'];
		$values = array();
		
		
		// set table name
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_values';
		
	 	if($sub_fields)
	 	{
		 	foreach($sub_fields as $sub_field)
		 	{
		 		// get var
			 	$db_values = $wpdb->get_results("SELECT value, order_no FROM $table_name WHERE field_id = '$sub_field->id' AND post_id = '$post_id' ORDER BY order_no ASC");
			 	
			 	if($db_values)
			 	{
			 		foreach($db_values as $db_value)
				 	{
				 		
				 		$value = $db_value->value;
				 		// format if needed
						if(method_exists($this->parent->fields[$sub_field->type], 'format_value_for_api'))
						{
							 $value = $this->parent->fields[$sub_field->type]->format_value_for_api($value);
						}
						
						//echo 'db order no = '.$db_value->order_no;
						$values[$db_value->order_no][$sub_field->name] = $value;
				 	}
	
			 	}
			 	else
			 	{
			 		$values[0][$sub_field->name] = false;
			 	}
			 			 	
		 	}
	 	}
	 	
	 	if(empty($values))
	 	{
	 		$values = false;
	 	}
	 	
	 	return $values;
	}
}

?>