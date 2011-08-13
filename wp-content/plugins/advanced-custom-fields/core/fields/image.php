<?php

class acf_Image
{
	var $name;
	var $title;
	var $parent;
	
	function acf_Image($parent)
	{
		$this->name = 'image';
		$this->title = __('Image','acf');
		$this->parent = $parent;
		
		add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
		add_filter('media_send_to_editor', array($this, 'media_send_to_editor'), 15, 2 );
		//add_action('admin_init', array($this, 'admin_init'));

	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * Options HTML
	 * - called from fields_meta_box.php
	 * - displays options in html format
	 *
	 * @author Elliot Condon
	 * @since 2.0.3
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function options_html($key, $options)
	{
		?>
		<tr class="field_option field_option_image">
			<td class="label">
				<label><?php _e("Save Format",'acf'); ?></label>
			</td>
			<td>
				<?php 
					$temp_field = new stdClass();	
					$temp_field->type = 'select';
					$temp_field->input_name = 'acf[fields]['.$key.'][options][save_format]';
					$temp_field->input_class = '';
					$temp_field->value = $options['save_format'];
					$temp_field->options = array('choices' => array(
						'url'	=>	'Image URL',
						'id'	=>	'Attachment ID'
					));
					$this->parent->create_field($temp_field);
				?>
			</td>
		</tr>

		<?php
	}


	 
	/*---------------------------------------------------------------------------------------------
	 * popup_head - STYLES MEDIA THICKBOX
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function popup_head()
	{
		if(isset($_GET["acf_type"]) && $_GET['acf_type'] == 'image')
		{
			?>
			<style type="text/css">
				#media-upload-header #sidemenu li#tab-type_url,
				#media-upload-header #sidemenu li#tab-gallery {
					display: none;
				}
				
				#media-items tr.url,
				#media-items tr.align,
				#media-items tr.image_alt,
				#media-items tr.image-size,
				#media-items tr.post_excerpt,
				#media-items tr.post_content,
				#media-items tr.image_alt p,
				#media-items table thead input.button,
				#media-items table thead img.imgedit-wait-spin,
				#media-items tr.submit a.wp-post-thumbnail {
					display: none;
				} 

				.media-item table thead img {
					border: #DFDFDF solid 1px; 
					margin-right: 10px;
				}

			</style>
			<script type="text/javascript">
			(function($){
			
				$(document).ready(function(){
				
					$('#media-items').bind('DOMNodeInserted',function(){
						$('input[value="Insert into Post"]').each(function(){
							$(this).attr('value','<?php _e("Select Image",'acf'); ?>');
						});
					}).trigger('DOMNodeInserted');
					
					$('form#filter').each(function(){
						
						$(this).append('<input type="hidden" name="acf_type" value="image" />');
						
					});
				});
							
			})(jQuery);
			</script>
			<?php
		}
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * rename_buttons - RENAMES MEDIA THICKBOX BUTTONS
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------
	function admin_init() 
	{
		//if(isset($_GET["acf_type"]) && $_GET["acf_type"] == "image")
		//{
		//	add_filter('gettext', array($this, 'rename_buttons'), 1, 3);
		//}
	}
	
	function rename_buttons($translated_text, $source_text, $domain) {
		if ($source_text == 'Insert into Post') {
			return __('Select Image', 'acf' );
		}
		
		return $translated_text;
	}*/
	
	
	/*---------------------------------------------------------------------------------------------
	 * media_send_to_editor - SEND IMAGE TO ACF DIV
	 *
	 * @author Elliot Condon
	 * @since 1.1.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function media_send_to_editor($html, $id)
	{
		parse_str($_POST["_wp_http_referer"], $arr_postinfo);
		
		if(isset($arr_postinfo["acf_type"]) && $arr_postinfo["acf_type"] == "image")
		{

			$file_src = wp_get_attachment_url($id);
		
			?>
			<script type="text/javascript">
				
				if(self.parent.acf_div.find('input.value').hasClass('id'))
				{
					self.parent.acf_div.find('input.value').val('<?php echo $id; ?>');
				}
				else
				{
					self.parent.acf_div.find('input.value').val('<?php echo $file_src; ?>');
				}
				
				
			 	self.parent.acf_div.find('img').attr('src','<?php echo $file_src; ?>');
			 	self.parent.acf_div.addClass('active');
			 	
			 	// reset acf_div and return false
			 	self.parent.acf_div = null;
			 	
			 	self.parent.tb_remove();
				
			</script>
			<?php
			exit;
		} 
		else 
		{
			return $html;
		}
		
	}
	
	
	function html($field)
	{
		
		$class = "";
		
		if($field->value != '')
		{
			$class = " active";
		}
		
		if(!isset($field->options['save_format'])){$field->options['save_format'] = 'url';}

		echo '<div class="acf_image_uploader'.$class.'">';
			echo '<a href="#" class="remove_image"></a>';
			if($field->options['save_format'] == 'id')
			{
				$file_src = wp_get_attachment_url($field->value);
				echo '<img src="'.$file_src.'" alt=""/>';
			}
			else
			{
				echo '<img src="'.$field->value.'" alt=""/>';
			}
			
			echo '<input class="value '.$field->options['save_format'].'" type="hidden" name="'.$field->input_name.'" value="'.$field->value.'" />';
			echo '<p>'.__('No image selected','acf').'. <input type="button" class="button" value="'.__('Add Image','acf').'" /></p>';
		echo '</div>';

	}
	
}

?>