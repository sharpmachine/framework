<?php

	/*---------------------------------------------------------------------------------------------
		Input Meta Box
	---------------------------------------------------------------------------------------------*/
	
	
	// vars
	global $post;
	$acfs = $args['args']['acfs'];
	$adv_options = $this->get_acf_options($acfs[0]->ID);
	
	
	
	
	
	$fields = array();
	
	foreach($acfs as $acf)
	{
		// get this acf's fields and add them to the global $fields
		$this_fields = $this->get_fields($acf->ID);
		foreach($this_fields as $this_field)
		{
			$fields[] = $this_field;
		}
	
	}
	
?>


<input type="hidden" name="ei_noncename" id="ei_noncename" value="<?php echo wp_create_nonce('ei-n'); ?>" />
<input type="hidden" name="input_meta_box" value="true" />


<style type="text/css">
	<?php if(!in_array('the_content',$adv_options->show_on_page)): ?>
		#postdivrich {display: none;}
	<?php endif; ?>
	
	<?php if(!in_array('custom_fields',$adv_options->show_on_page)): ?>
		#postcustom,
		#screen-meta label[for=postcustom-hide] {display: none;}
	<?php endif; ?>
	
	<?php if(!in_array('discussion',$adv_options->show_on_page)): ?>
		#commentstatusdiv,
		#screen-meta label[for=commentstatusdiv-hide] {display: none;}
	<?php endif; ?>
	
	<?php if(!in_array('comments',$adv_options->show_on_page)): ?>
		#commentsdiv,
		#screen-meta label[for=commentsdiv-hide] {display: none;}
	<?php endif; ?>
	
	<?php if(!in_array('slug',$adv_options->show_on_page)): ?>
		#slugdiv,
		#screen-meta label[for=slugdiv-hide] {display: none;}
	<?php endif; ?>
	
	<?php if(!in_array('author',$adv_options->show_on_page)): ?>
		#authordiv,
		#screen-meta label[for=authordiv-hide] {display: none;}
	<?php endif; ?>
	
	#screen-meta label[for=acf_input-hide] {display: none;}
</style>


<div class="acf_fields_input">

	<?php 
	
	$i = 0;
	foreach($acfs as $acf)
	{
	
		// load acf data
		$options = $this->get_acf_options($acf->ID);
		$fields = $this->get_fields($acf->ID);
		$html = '';
		
		
		if($options->field_group_layout == "in_box")
		{
			echo '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>'.$acf->post_title.'</span></h3><div class="inside">';
		}


		foreach($fields as $field)
		{
		
			// if they didn't select a type, skip this field
			if($field->type == 'null')
			{
				continue;
			}
			
			
			// set value, id and name for field
			$field->value_id = $this->load_value_id_input($post->ID, $field);
			$field->value = $this->load_value_for_input($post->ID, $field);
			$field->input_name = 'acf['.$i.'][value]';
			$field->input_class = '';
			
			
			echo '<div class="field">';
			
				echo '<input type="hidden" name="acf['.$i.'][field_id]" value="'.$field->id.'" />';
				echo '<input type="hidden" name="acf['.$i.'][field_type]" value="'.$field->type.'" />';
				echo '<input type="hidden" name="acf['.$i.'][value_id]" value="'.$field->value_id.'" />';
				
				if($field->save_as_cf == 1)
				{
					echo '<input type="hidden" name="acf['.$i.'][save_as_cf]" value="'.$field->name.'" />';	
				}
				
				
				echo '<label for="'.$field->input_name.'">'.$field->label.'</label>';
			
				
				if($field->instructions)
				{
					echo '<p class="instructions">'.$field->instructions.'</p>';
				}
				
				
				$this->create_field($field);
		
			echo '</div>';
			
			$i++;
		} 
		
		
		if($options->field_group_layout == "in_box")
		{
			echo '</div></div>';
		}
	}

	?>
	
</div>
