<?php
/*
Plugin Name: Perfect Quotes
Plugin URI: http://www.perfectspace.com/perfect-quotes
Description: An easy to use plugin for quotes and testimonials! It integrates easily using a widget or shortcode. It does quotes perfectly!
Version: 0.3.4.4
Author: Brandon Ferens
Author URI: http://www.perfectspace.com
License: GPL2
*/

require 'includes/post-type.php';
require 'includes/widget.php';
require 'includes/widget-single.php';
require 'includes/shortcodes.php';
require 'includes/metaboxes.php';

add_action('admin_head', 'perfect_quotes_admin_css');

// Custom Columns
add_action("manage_posts_custom_column",  "perfect_quotes_columns");
add_filter("manage_edit-perfect-quotes_columns", "perfect_quotes_edit_columns");

function perfect_quotes_edit_columns($columns){
  $columns = array(
    'cb'                   => "<input type=\"checkbox\" />",
    'title'                => 'Quote',
    'perfect-quote-author' => 'Author',
    'shortcode'            => 'Shortcode',
    'category'             => 'Category',
    'author'               => 'Posted by',
    'date'                 => 'Date'
  );

  return $columns;
}

function perfect_quotes_columns($column){
  global $post;

  switch ($column) {
    case 'perfect-quote-author':
      echo get_post_meta($post->ID, 'perfect_quote_author', true);
      break;
    case 'shortcode':
      echo '[perfect_quotes id="' . $post->ID . '"]';
      break;
    case 'category':
      the_terms( $post->ID, 'perfect_quotes_category');
      break;
  }
}

// Change the default "Enter title here" text
function perfect_quotes_post_title($title) {
  $screen = get_current_screen();
  if ('perfect-quotes' == $screen->post_type) {
    $title = 'Enter Perfect Quote here';
  }
  return $title;
}
add_filter('enter_title_here', 'perfect_quotes_post_title');

// Add filter for Perfect Quotes
add_filter( 'post_updated_messages', 'perfect_quote_updated_messages' );
function perfect_quote_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['perfect-quotes'] = array(
    0  => '', // Unused. Messages start at index 1.
    1  => sprintf( __('Perfect Quote updated. <a href="%s">View quote</a>'), esc_url( get_permalink($post_ID) ) ),
    2  => __('Custom field updated.'),
    3  => __('Custom field deleted.'),
    4  => __('Perfect Quote updated.'),
    5  => isset($_GET['revision']) ? sprintf( __('Perfect Quote restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6  => sprintf( __('Perfect Quote published. <a href="%s">View quote</a>'), esc_url( get_permalink($post_ID) ) ),
    7  => __('Perfect Quote saved.'),
    8  => sprintf( __('Perfect Quote submitted. <a target="_blank" href="%s">Preview quote</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9  => sprintf( __('Perfect Quote scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview quote</a>'),
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Perfect Quote draft updated. <a target="_blank" href="%s">Preview quote</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

// Display contextual help for Perfect Quotes
add_action( 'contextual_help', 'perfect_quote_add_help_text', 10, 3 );

function perfect_quote_add_help_text( $contextual_help, $screen_id, $screen ) {
  if ( 'perfect-quotes' == $screen->id ) {
    $contextual_help =
      '<p><strong>' . __('Things to remember when adding or editing a <em>Perfect Quote</em>:') . '</strong></p>' .
      '<ul>' .
        '<li>' . __('Just type in the <em>Perfect Quote</em> you want! It\'s that easy!') . '</li>' .
        '<li>' . __('If you want to include the source of the quote, just add it in the appropriate input field!') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('If you want to schedule the <em>Perfect Quote</em> to be published in the future:') . '</strong></p>' .
      '<ul>' .
        '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
        '<li>' . __('Change the date to when you actually publish the quote, then click on OK.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more information:') . '</strong></p>' .
      '<p>' . __('<a href="http://perfectspace.com/" target="_blank">Visit PerfectSpace.com</a>') . '</p>';
  }
  return $contextual_help;
}

function perfect_quotes_meta_boxes() {
  global $post;
  $pagename = 'perfect-quotes';
  add_meta_box( 'perfect_quotes_form', 'Perfect Quote', 'perfect_quotes_form', $pagename, 'normal', 'high' );
}

function perfect_quotes_save_postdata($post_id) {
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  } // end if

  // Check user permissions
  if (isset($_POST['post_type']) && $_POST['post_type'] == 'page') {
    if (!current_user_can('edit_page', $post_id)) return $post_id;
  } else {
    if (!current_user_can('edit_post', $post_id)) return $post_id;
  }

  // OK, we're authenticated: we need to find and save the data
  $current_author = get_post_meta($post_id, 'perfect_quote_author', false);
  $new_author = (isset($_POST['perfect_quote_author'])) ? $_POST['perfect_quote_author'] : '';
  $current_where  = get_post_meta($post_id, 'perfect_quote_where', false);
  $new_where = (isset($_POST['perfect_quote_where'])) ? $_POST['perfect_quote_where'] : '' ;

  perfect_quotes_clean($new_author);
  perfect_quotes_clean($new_where);

  if (!empty($current_author)) {
    if (is_null($new_author)) {
      delete_post_meta($post_id,'perfect_quote_author');
    } else {
      update_post_meta($post_id,'perfect_quote_author',$new_author);
    }
  } elseif (!is_null($new_author)) {
      add_post_meta($post_id,'perfect_quote_author',$new_author,true);
  }

  if (!empty($current_where)) {
    if (is_null($new_where)) {
      delete_post_meta($post_id,'perfect_quote_where');
    } else {
      update_post_meta($post_id,'perfect_quote_where',$new_where);
    }
  } elseif (!is_null($new_where)) {
      add_post_meta($post_id,'perfect_quote_where',$new_where,true);
  }

  return $post_id;
}

function perfect_quotes_clean(&$arr) {
  if (is_array($arr)) {
    foreach ($arr as $i => $v) {
      if (is_array($arr[$i])) {
        my_meta_clean($arr[$i]);
        if (!count($arr[$i])) {
          unset($arr[$i]);
        }
      } else {
        if (trim($arr[$i]) == '') {
          unset($arr[$i]);
        }
      }
    }
    if (!count($arr)) {
      $arr = NULL;
    }
  }
}

function perfect_quotes_admin_css() {
  echo '<link rel="stylesheet" type="text/css" href="'.plugin_dir_url(__FILE__) . 'includes/admin.css" />';
}

// Function to retrieve all Perfect Quote categories
function get_perfect_quotes_terms($parent = null, $hide_empty = false) {
  $args = array(
    'hide_empty' => $hide_empty,
    'orderby' => 'name',
  );
  if ($parent !== null) {
    $args['parent'] = $parent;
  }

  $terms = get_terms(
    'perfect_quotes_category',
    $args
  );

  // foreach($terms as $key => $term) {
  //   $terms[$key]->children = get_perfect_quotes_terms($term->term_id);
  // }
  return $terms;
}
