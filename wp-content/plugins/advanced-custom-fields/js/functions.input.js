(function($){

	// exists
	$.fn.exists = function()
	{
		return $(this).length>0;
	};
	
	// vars
	var wysiwyg_count = 0;
	var post_id = 0;

	// global vars
	window.acf_div = null;
	
	
	/*-------------------------------------------
		Wysiwyg
	-------------------------------------------*/
	$.fn.make_acf_wysiwyg = function()
	{	
		wysiwyg_count++;
		var id = 'acf_wysiwyg_'+wysiwyg_count;
		//alert(id);
		$(this).find('textarea').attr('id',id);
		tinyMCE.execCommand('mceAddControl', false, id);
	};
	
	/*-------------------------------------------
		Datepicker
	-------------------------------------------*/
	$.fn.make_acf_datepicker = function()
	{
		var format = 'dd/mm/yy';
		if($(this).siblings('input[name="date_format"]').val() != '')
		{
			format = $(this).siblings('input[name="date_format"]').val();
		}
		
		$(this).datepicker({ 
			dateFormat: format 
		});
		
		$('#ui-datepicker-div').wrap('<div class="acf_datepicker" />');
	};
	
	
	/*-------------------------------------------
		Image Upload
	-------------------------------------------*/
	$.fn.make_acf_image = function(){
	
		var div = $(this);
		
		div.find('input.button').click(function(){
			
			// set global var
			window.acf_div = div;
			
			
			// show the thickbox
			tb_show('Add Image to field', 'media-upload.php?type=image&acf_type=image&TB_iframe=1');
			
				
			return false;
		});
		
		
		div.find('a.remove_image').unbind('click').click(function()
		{
			div.find('input.value').val('');
			div.removeClass('active');
		
			return false;
		});
	};
	
	
	/*-------------------------------------------
		File Upload
	-------------------------------------------*/
	$.fn.make_acf_file = function(){
	
		var div = $(this);

		
		div.find('p.no_file input.button').click(function(){
			
			// set global var
			window.acf_div = div;
			
			
			// show the thickbox
			tb_show('Add File to field', 'media-upload.php?type=file&acf_type=file&TB_iframe=1');
			
			
			return false;
		});
		
		
		
		div.find('p.file input.button').unbind('click').click(function()
		{
			div.find('input.value').val('');
			div.removeClass('active');
		
			return false;
		});
	};

	
	
	/*-------------------------------------------
		Repeaters
	-------------------------------------------*/
	$.fn.make_acf_repeater = function(){
		
		// vars
		var div = $(this);
		var add_field = div.find('a#add_field');
		var row_limit = parseInt(div.children('input[name="row_limit"]').val());
		
		
		/*-------------------------------------------
			Add Field Button
		-------------------------------------------*/
		add_field.unbind("click").click(function(){
			
			var field_count = div.children('table').children('tbody').children('tr').length;
			if(field_count >= row_limit)
			{
				// reached row limit!
				
				add_field.attr('disabled','true');
				return false;
			}
			
			// clone last tr
			var new_field = div.children('table').children('tbody').children('tr').last().clone(false);
			
			// append to table
			div.children('table').children('tbody').append(new_field);
			
			// set new field
			new_field.reset_values();
			
			// re make special fields
			new_field.make_all_fields();
						
			// update order numbers
			update_order_numbers();
			
			if(div.children('table').children('tbody').children('tr').length > 1)
			{
				div.removeClass('hide_remove_buttons');
			}
			
			if((field_count+1) >= row_limit)
			{
				// reached row limit!
				add_field.attr('disabled','true');
			}
			
			return false;
			
		});
		
		div.add_remove_buttons();
		
		if(row_limit > 1){
			div.make_sortable();
		}
		
		
		if(div.children('table').children('tbody').children('tr').length == 1)
		{
			div.addClass('hide_remove_buttons');
		}
		
		var field_count = div.children('table').children('tbody').children('tr').length;
		if(field_count >= row_limit)
		{
			add_field.attr('disabled','true');
		}
		
	};
	
	
	/*-------------------------------------------
		Update Order Numbers
	-------------------------------------------*/
	function update_order_numbers(){
		$('.postbox#acf_input .repeater').each(function(){
			$(this).children('table').children('tbody').children('tr').each(function(i){
				$(this).children('td.order').html(i+1);
			});
	
		});
	}
	
	/*-------------------------------------------
		Sortable
	-------------------------------------------*/
	$.fn.make_sortable = function(){
		
		//alert('make sortable');
		var div = $(this);
		
		var fixHelper = function(e, ui) {
			ui.children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};
		
		div.children('table').children('tbody').unbind('sortable').sortable({
			update: function(event, ui){
				update_order_numbers();
				$(this).make_all_fields();
				//alert('update');
				},
			handle: 'td.order',
			helper: fixHelper,
			//pre process stuff as soon as the element has been lifted
		    start: function(event, ui)
		    {
				//console.log(ui.item);
				if(ui.item.find('.acf_wysiwyg').exists())
				{
					//console.log('aaaah, i found a wysiwyg')
					var id = ui.item.find('.acf_wysiwyg textarea').attr('id');
					//alert(tinyMCE.get(id).getContent());
					tinyMCE.execCommand("mceRemoveControl", false, id);
				}
		    },
		
		    //post process stuff as soon as the element has been dropped
		    stop: function(event, ui)
		    {
				if(ui.item.find('.acf_wysiwyg').exists())
				{
					var id = ui.item.find('.acf_wysiwyg textarea').attr('id');
					tinyMCE.execCommand("mceAddControl", false, id);
					//div.make_sortable();
				}
		    }
		});
	}
	
	
	
	/*-------------------------------------------
		Reset Values
	-------------------------------------------*/
	$.fn.reset_values = function(){
		
		var div = $(this);
		
		
		if(div.find('.acf_wysiwyg').exists())
		{
			var wysiwyg = $(this).find('.acf_wysiwyg');
			
			var name = wysiwyg.find('textarea').first().attr('name');
			
			wysiwyg.html('<textarea name="'+name+'"></textarea>');
		}
		
		
		// image upload
		div.find('.acf_image_uploader').each(function(){
			$(this).removeClass('active');
		});
		
		
		// file upload
		div.find('.acf_file_uploader').each(function(){
			$(this).removeClass('active');
		});
		
		
		// date picker
		div.find('.acf_datepicker').each(function(){
			$(this).removeClass('hasDatepicker');
		});
		
		
				

		// total fields
		var total_fields = $(this).siblings('tr').length;

		
		// reset all values
		$(this).find('[name]').each(function()
		{
			var name = $(this).attr('name').replace('[value]['+(total_fields-1)+']','[value]['+(total_fields)+']');
			$(this).attr('name', name);
			$(this).attr('id', name);
			
			if(name.indexOf("[field_id]") != -1)
			{
				// do nothing, we want to keep this hidden field with it's current values
			}
			else if(name.indexOf("[field_type]") != -1)
			{
				// do nothing, we want to keep this hidden field with it's current values
			}
			else if(name.indexOf("date_format") != -1)
			{
				// do nothing, we want to keep this hidden field with it's current values
			}
			else
			{
				$(this).val('');
			}
			
			// selected / ticked
			if($(this).is(':selected'))
			{
				$(this).removeAttr('selected');
				
			}
			else if($(this).is(':checked'))
			{
				$(this).removeAttr('checked');
			}			
			
		});
		
		
	};
	
	$.fn.make_all_fields = function()
	{
		var div = $(this);
		
		// wysiwyg
		div.find('.acf_wysiwyg').each(function(){
			$(this).make_acf_wysiwyg();	
		});
		
		// datepicker
		div.find('.acf_datepicker').each(function(){
			$(this).make_acf_datepicker();
		});
		
		// image
		div.find('.acf_image_uploader').each(function(){
			$(this).make_acf_image();
		});
		
		// file
		div.find('.acf_file_uploader').each(function(){
			$(this).make_acf_file();
		});
	};
	
	
	/*-------------------------------------------
		Remove Field Button
	-------------------------------------------*/
	$.fn.add_remove_buttons = function(){
		$(this).find('a.remove_field').unbind('click').live('click', function(){

			var total_fields = $(this).closest('.repeater').children('table').children('tbody').children('tr').length;
			
			// needs at least one
			if(total_fields <= 1)
			{
				return false;
			}
			else if(total_fields == 2)
			{
				// total fields will be 1 after the tr is removed
				$(this).parents('.repeater').addClass('hide_remove_buttons');
			}
			
			var tr = $(this).closest('tr');
			
			tr.animate({'opacity':'0'}, 300,function(){
				tr.remove();
				update_order_numbers();
			});
			
			
			$(this).closest('.repeater').find('a#add_field').removeAttr('disabled');
			
			
			return false;
			
		});
	};
	
	
	
	
	/*-------------------------------------------
		Document Ready
	-------------------------------------------*/
	$(document).ready(function(){
		
		
		post_id = $('form#post input#post_ID').val();
		var div = $('#acf_input');
		
		
		if(typeof(tinyMCE) != "undefined")
		{
			if(tinyMCE.settings.theme_advanced_buttons1)
			{
				tinyMCE.settings.theme_advanced_buttons1 += ",|,add_image,add_video,add_audio,add_media";
			}
			
			if(tinyMCE.settings.theme_advanced_buttons2)
			{
				tinyMCE.settings.theme_advanced_buttons2 += ",code";
			}
		}

		
		div.make_all_fields();
		
		// repeater
		div.find('.repeater').each(function(){
			$(this).make_acf_repeater();
		});
		
		
	});
	
})(jQuery);
