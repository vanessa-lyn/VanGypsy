<?php
/**
 * Perfect Quotes custom post type
 */
function perfect_quotes_init() {
  $labels = array(
    'name'               => __('Perfect Quotes', 'post type general name'),
    'singular_name'      => __('Perfect Quote', 'post type singular name'),
    'add_new'            => __('Add new', 'member'),
    'add_new_item'       => __('Add new Perfect Quote'),
    'edit_item'          => __('Edit Perfect Quote'),
    'new_item'           => __('New Perfect Quote'),
    'view_item'          => __('View Perfect Quote'),
    'search_items'       => __('Search Perfect Quotes'),
    'not_found'          =>  __('No Perfect Quotes found!'),
    'not_found_in_trash' => __('No Perfect Quotes in the trash!'),
    'parent_item_colon'  => ''
  );

  $args = array(
    'labels'               => $labels,
    'public'               => true,
    'publicly_queryable'   => true,
    'show_ui'              => true,
    'query_var'            => true,
    'rewrite'              => array('slug' => 'perfect-quotes'),
    'capability_type'      => 'post',
    'hierarchical'         => false,
    'has_archive'          => true,
    'menu_position'        => 100,
    'menu_icon'            => plugins_url('images/perfect-space-icon.png', dirname(__FILE__)),
    'supports'             => array('title'),
    'register_meta_box_cb' => 'perfect_quotes_meta_boxes'
  );

  register_post_type('perfect-quotes',$args);
  add_action( 'save_post', 'perfect_quotes_save_postdata' );
}
add_action('init', 'perfect_quotes_init');

/**
 * Perfect Quotes Category taxonomy
 */
function perfect_quotes_taxonomy_init() {
  $labels = array(
    'name'              => 'Categories',
    'singular_name'     => 'Category',
    'search_items'      => 'Search Categories',
    'all_items'         => 'All Categories',
    'parent_item'       => 'Parent Category',
    'parent_item_colon' => 'Parent Category:',
    'edit_item'         => 'Edit Category',
    'update_item'       => 'Update Category',
    'add_new_item'      => 'Add New Category',
    'new_item_name'     => 'New Category Name',
    'menu_name'         => 'Categories'
  );

  $args = array(
    'hierarchical' => true,
    'labels'       => $labels,
    'show_ui'      => true,
    'query_var'    => true,
    'rewrite'      => array('slug' => 'perfect-quotes-categories'),
  );
  register_taxonomy('perfect_quotes_category', 'perfect-quotes', $args);
}
add_action('init', 'perfect_quotes_taxonomy_init');
