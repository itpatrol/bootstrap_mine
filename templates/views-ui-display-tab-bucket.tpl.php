<?php
/**
 * @file
 * Template for each "box" on the display query edit screen.
 */
?>
<div class="<?php print implode(' ', $classes); ?> panel panel-default"<?php print backdrop_attributes($attributes); ?>>
  <?php print $item_help_icon; ?>
  <?php if(!empty($actions)) : ?>
    <?php print $actions; ?>
  <?php endif; ?>
  <?php if (!empty($title)) : ?>
    <div class="panel-heading">
    <h3 class="panel-title"><?php print $title; ?></h3>
    </div>
  <?php endif; ?>
  <div class="panel-body">
  <?php print $content; ?>
  </div>
</div>
