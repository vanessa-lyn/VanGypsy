<?php
function perfect_quotes_form() {

  global $post;
  $author = get_post_meta($post->ID, 'perfect_quote_author', true);
  $where  = get_post_meta($post->ID, 'perfect_quote_where', true);
  ?>
  <div class="input-div">
    <label class="pq-field-label" for="perfect_quote_author">Quote Author (optional)</label>
    <input type="text" value="<?php echo $author; ?>" id="perfect_quote_author" class="text" name="perfect_quote_author">
  </div>
  <div class="input-div last">
    <label class="pq-field-label" for="perfect_quote_where">Where is the quote or author from? (optional)</label>
    <input type="text" value="<?php echo $where; ?>" id="perfect_quote_where" class="text" name="perfect_quote_where">
  </div>
  <?php
}
