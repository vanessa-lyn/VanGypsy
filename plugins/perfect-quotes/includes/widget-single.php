<?php
add_action('widgets_init', 'perfect_quotes_single_register_widget');

function perfect_quotes_single_register_widget() {
  register_widget('Perfect_Quotes_Single_Widget');
}

class Perfect_Quotes_Single_Widget extends WP_Widget {

  function __construct() {
    $widget_ops = array(
      'classname'   => 'widget_perfect_quote_single',
      'description' => __('A quotes widget perfectly done')
    );
    parent::__construct('perfect-quotes-single', __('Perfect Quotes - Single Quote'), $widget_ops);
    $this->alt_option_name = 'widget_perfect_quote_single';

    add_action( 'save_post', array(&$this, 'flush_widget_cache') );
    add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
    add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
  }

  function widget($args, $instance) {
    if (empty($instance['quote'])) {
      return;
    }

    // Retrieve cached data
    $cache = wp_cache_get('widget_perfect_quotes_single', 'widget');

    // Load Perfect Quotes style.css
    wp_enqueue_style('perfect_quotes', plugins_url('style.css', __FILE__));

    if (!is_array($cache)) {
      $cache = array();
    }

    if (isset($cache[$args['widget_id']])) {
      echo $cache[$args['widget_id']];
      return;
    }

    // We don't have cached data : we create it!
    ob_start();
    extract($args);
    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

    $query = array(
      'p'         => $instance['quote'],
      'post_type' => 'perfect-quotes'
    );

    $r = new WP_Query($query);

    if ($r->have_posts()) {
      echo $before_widget;
      if ($title) {
        echo $before_title . $title . $after_title;
      }
      echo '<ul class="perfect-quotes">';
      while ($r->have_posts()) {
        $r->the_post();
        ?>
        <li>
          <?php
          if (get_the_title()) the_title();
          $quote_author = get_post_meta(get_the_ID(), 'perfect_quote_author', true);
          $quote_where  = get_post_meta(get_the_ID(), 'perfect_quote_where', true);
          ?>
          <span>
            <?php
            if (!empty($quote_author)) echo $quote_author;
            if (!empty($quote_author) && !empty($quote_where)) echo '<br />';
            if (!empty($quote_where)) echo $quote_where;
            ?>
          </span>
        </li>
        <?php
      }
      echo '</ul>';
      echo $after_widget;

      // Reset the global $the_post as this query will have stomped on it
      wp_reset_postdata();
    }

    // Echo the result get it for caching
    $cache[$args['widget_id']] = ob_get_flush();
    wp_cache_set('widget_perfect_quotes_single', $cache, 'widget');
  }

  function update($new_instance, $old_instance) {
    $instance           = $old_instance;
    $instance['title']  = strip_tags($new_instance['title']);
    $instance['quote']  = $new_instance['quote'];
    // Keep the data fresh
    $this->flush_widget_cache();

    $alloptions = wp_cache_get('alloptions', 'options');
    if (isset($alloptions['widget_perfect_quote_single'])) {
      delete_option('widget_perfect_quote_single');
    }

    return $instance;
  }

  function flush_widget_cache() {
    wp_cache_delete('widget_perfect_quotes_single', 'widget');
  }

  function form($instance) {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : 'Perfect Quotes';
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title: (optional)'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <p>
      <label for="<?php echo $this->get_field_id('quote'); ?>"><?php echo __('Select Perfect Quote:'); ?></label>
      <?php
      $quotes = get_posts(array('post_type' => 'perfect-quotes'));
      foreach ($quotes as $quote) {
        $quote_data[$quote->ID] = (strlen($quote->post_title) > 40) ? substr($quote->post_title, 0, 37) . '...' : $quote->post_title;
      }
      ?>
      <select class="widefat" id="<?php echo $this->get_field_id('quote'); ?>" name="<?php echo $this->get_field_name('quote'); ?>">
        <option value="">Select a quote...</option>

        <?php foreach ($quote_data as $key => $val) : ?>
          <?php $selected = ($key == $instance['quote']) ? ' selected' : ' nope'; ?>
          <option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>
        <?php endforeach; ?>
      </select>
    </p>
    <?php
  }
}
