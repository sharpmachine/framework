<?php

class acf_Textarea
{
	var $name;
	var $title;
	
	function acf_Textarea()
	{
		$this->name = 'textarea';
		$this->title = __("Text Area",'acf');
	}
	
	function html($field)
	{
		// remove unwanted <br /> tags
		$field->value = str_replace('<br />','',$field->value);
		echo '<textarea id="'.$field->input_name.'" rows="4" class="'.$field->input_class.'" name="'.$field->input_name.'" >'.$field->value.'</textarea>';
	}
	
	function format_value_for_input($value)
	{
		$value = htmlspecialchars($value, ENT_QUOTES);
		return $value;
	}
	
	function format_value_for_api($value)
	{
		$value = nl2br($value);
		return $value;
	}
}

?>