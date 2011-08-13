<?php

$labels = array(
    'name' => __( 'Advanced&nbsp;Custom&nbsp;Fields', 'acf' ),
	'singular_name' => __( 'Advanced Custom Fields', 'acf' ),
    'add_new' => __( 'Add New' , 'acf' ),
    'add_new_item' => __( 'Add New Advanced Custom Field Group' , 'acf' ),
    'edit_item' =>  __( 'Edit Advanced Custom Field Group' , 'acf' ),
    'new_item' => __( 'New Advanced Custom Field Group' , 'acf' ),
    'view_item' => __('View Advanced Custom Field Group'),
    'search_items' => __('Search Advanced Custom Field Groups'),
    'not_found' =>  __('No Advanced Custom Field Groups found'),
    'not_found_in_trash' => __('No Advanced Custom Field Groups found in Trash'), 
);


$supports = array(
	'title',
	//'revisions',
	//'custom-fields',
	'page-attributes'
);

register_post_type('acf', array(
	'labels' => $labels,
	'public' => false,
	'show_ui' => true,
	'_builtin' =>  false,
	'capability_type' => 'page',
	'hierarchical' => true,
	'rewrite' => array("slug" => "acf"),
	'query_var' => "acf",
	'supports' => $supports,
));

?>