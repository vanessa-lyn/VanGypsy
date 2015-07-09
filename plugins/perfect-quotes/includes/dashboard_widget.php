<?php

function add_testimonial_dashboard_widgets() {
  global $wp_meta_boxes;
  wp_add_dashboard_widget('testimonials_widget', 'Testimonial QuickPress', 'testimonial_quickpress');
}

function testimonial_quickpress() {
  echo '<p>Welcome to Custom Blog Theme! Need help? Contact the developer <a href="mailto:yourusername@gmail.com">here</a>. For WordPress Tutorials visit: <a href="http://www.wpbeginner.com" target="_blank">WPBeginner</a></p>';
}
