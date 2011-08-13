<?php

class acf_Text
{
	var $name;
	var $title;
	
	function acf_Text()
	{
		$this->name = 'text';
		$this->title = __("Text",'acf');
	}
	
	function html($field)
	{
		echo '<input type="text" value="'.$field->value.'" id="'.$field->input_name.'" class="'.$field->input_class.'" name="'.$field->input_name.'" />';
	}
	
	function format_value_for_input($value)
	{
		return htmlspecialchars($value, ENT_QUOTES);
	}
	
}

?>