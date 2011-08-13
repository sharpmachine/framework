<?php 

/*---------------------------------------------------------------------------------------------
 * Global Vars for Groovy Repeater while loop
 *
 * @author Elliot Condon
 * @since 2.0.3
 * 
 ---------------------------------------------------------------------------------------------*/
global $repeater_row_count, $repeater_row;

$repeater_row_count = 0;
$repeater_row = null;



/*---------------------------------------------------------------------------------------------
 * acf_object
 *
 * @author Elliot Condon
 * @since 1.0.0
 * 
 ---------------------------------------------------------------------------------------------*/
class acf_object
{
    function acf_object($variables)
    {
    	foreach($variables as $key => $value)
    	{
    		// field may exist but field name may be blank!!!
    		if($key)
    		{
    			$this->$key = $value;
    		}
       	}
    }
    
}

/*---------------------------------------------------------------------------------------------
 * get_acf
 *
 * @author Elliot Condon
 * @since 1.0.0
 * 
 ---------------------------------------------------------------------------------------------*/
function get_acf($post_id = false)
{
	// get global vars
	global $acf;
	global $post;
	
	
	// create blank arrays
	$fields = array();
	$variables = array();
	
	
	// if no ID was passed through, just use the $post->ID
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 0;
	}
	

    global $wpdb;
	$acf_fields = $wpdb->prefix.'acf_fields';
	$acf_values = $wpdb->prefix.'acf_values';	 	
		 
		 	
	// get fields
	$fields = array();
   	$temp_fields = $wpdb->get_results("SELECT DISTINCT f.* FROM $acf_fields f 
   	LEFT JOIN $acf_values v ON v.field_id=f.id
   	WHERE v.post_id = '$post_id'");
   	

    if(empty($temp_fields)){return null;}
    
	
	// add fields to field array with key = field->id
	foreach($temp_fields as $field)
	{
		$fields[$field->id] = $field;
	}
	
	
	// now look for child fields
	foreach($fields as $i => $field)
	{
		if($field->parent_id != 0)
		{
			// this is a sub field.
			$parent_field = $wpdb->get_row("SELECT * FROM $acf_fields WHERE id = $field->parent_id");
			
			if(isset($fields[$parent_field->id]))
			{
				// parent field has already been created!
				$fields[$parent_field->id]->options['sub_fields'][] = $field;
			}
			else
			{
				// add sub field to parent field
				$parent_field->options = array();
				$parent_field->options['sub_fields'][] = $field;
			
			
				// add parent field
				$fields[$parent_field->id] = $parent_field;
			}
			
			
			unset($fields[$i]);
		}
	}
	
	foreach($fields as $field)
	{
	
		// add this field: name => value
		$variables[$field->name] = $acf->load_value_for_api($post_id, $field);
		
	}
	
	
	// create a new obejct and give in variables
	$object = new stdClass();
	
	foreach($variables as $key => $value)
	{
		if (empty($key))
		{
			continue;
		}
		
		$object->$key = $value;
	}
	
	
	// return the object
	return $object;
	
	  
}


// get fields
function get_fields($post_id = false)
{
	return get_acf($post_id);
}


// get field
function get_field($field_name, $post_id = false)
{
	global $acf_fields;
	global $post;
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	
	//echo 'field name: '.$field_name.', post id: '.$post_id;
	
	if(!isset($acf_fields))
	{
		$acf_fields = array();
	}
	if(!isset($acf_fields[$post_id]))
	{
		$acf_fields[$post_id] = get_acf($post_id);
	}
	
	return $acf_fields[$post_id]->$field_name;
}


// the field
function the_field($field_name, $post_id = false)
{
	//echo 'field name: '.$field_name.', post id: '.$post_id;
	$value = get_field($field_name, $post_id);
	
	if(is_array($value))
	{
		$value = @implode(', ',$value);
	}
	
	echo $value;
	
	
}


/*---------------------------------------------------------------------------------------------
 * Repeater Field functions
 *
 * @author Elliot Condon
 * @since 2.0.3
 * 
 ---------------------------------------------------------------------------------------------*/

function the_repeater_field($field_name, $post_id = false)
{
	global $repeater_row_count, $repeater_row;
	
	$rows = get_field($field_name, $post_id);

	if(isset($rows[$repeater_row_count]))
	{
		$repeater_row = $rows[$repeater_row_count];
		$repeater_row_count ++;
		return true;
	}
	
	
	// reset the vars and return false to end the loop
	
	$repeater_row_count = 0;
	$repeater_row = null;
	
	return false;
	
}


// get sub field
function get_sub_field($field_name, $field = false)
{
	
	
	if($field == false)
	{
		global $repeater_row;
		$field = $repeater_row;
	}
	
	if(isset($field[$field_name]))
	{
		return $field[$field_name];
	}
	else
	{
		return false;
	}
}


// get sub field
function the_sub_field($field_name, $field = false)
{
	$value = get_sub_field($field_name, $field);
	
	if(is_array($value))
	{
		$value = implode(', ',$value);
	}
	
	echo $value;
}



/*---------------------------------------------------------------------------------------------
 * ACF_WP_Query
 *
 * @author Elliot Condon
 * @since 1.1.3
 * 
 ---------------------------------------------------------------------------------------------*/
class ACF_WP_Query extends WP_Query 
{
	var $orderby_field;
	var $order;
	var $orderby_type;
	
	function __construct($args=array())
	{
		// set default variabls
		$this->orderby_field = '';
		$this->order = 'ASC';
		$this->orderby_type = 'string';
		
		
		// set order
		if(!empty($args['order']))
		{
			$this->order = $args['order'];
		}
		
		
		// set value type
		if(!empty($args['orderby_type']))
		{
			$this->orderby_type = $args['orderby_type'];
		}
		
		
		if(!empty($args['orderby_field']))
		{
			$this->orderby_field = $args['orderby_field'];
			
			add_filter('posts_join', array($this, 'posts_join'));
			add_filter('posts_where', array($this, 'posts_where'));
			add_filter('posts_orderby', array($this, 'posts_orderby'));
		}
		
		parent::query($args);
	}
	
	function posts_join($join)
	{
		global $wpdb;
		$acf_fields = $wpdb->prefix.'acf_fields';
		$acf_values = $wpdb->prefix.'acf_values';	
	
		$join .= "LEFT JOIN $acf_values v ON v.post_id=".$wpdb->prefix."posts.ID
		LEFT JOIN $acf_fields f ON f.id=v.field_id";
			
		return $join;
	}
	
	function posts_where($where)
	{
		$where .= "AND f.name = '".$this->orderby_field."'";
	  	return $where;
	}
	
	function posts_orderby($orderby)
	{
	
		if($this->orderby_type == 'int')
		{
			$orderby = "ABS(v.value) ".$this->order;
		}
		else
		{
			$orderby = "v.value ".$this->order;
		}
		

		return $orderby;
	}
}

?>