<?php

/**
 * @file
 * Default theme implementation for comments.
 *
 * Available variables:
 * - $author: Comment author. Can be link or plain text.
 * - $content: An array of comment items. Use render($content) to print them
 *   all, or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $created: Formatted date and time for when the comment was created.
 *   Preprocess functions can reformat it by calling format_date() with the
 *   desired parameters on the $comment->created variable.
 * - $changed: Formatted date and time for when the comment was last changed.
 *   Preprocess functions can reformat it by calling format_date() with the
 *   desired parameters on the $comment->changed variable.
 * - $new: New comment marker.
 * - $permalink: Comment permalink.
 * - $submitted: Submission information created from $author and $created during
 *   template_preprocess_comment().
 * - $user_picture: The comment author's picture from user-picture.tpl.php.
 * - $signature: Authors signature.
 * - $status: Comment status. Possible values are:
 *   comment-unpublished, comment-published or comment-preview.
 * - $title: Linked title.
 * - $classes: Array of classes that can be used to style contextually through
 *   CSS. The default values can be one or more of the following:
 *   - comment: The current template type, i.e., "theming hook".
 *   - comment-by-anonymous: Comment by an unregistered user.
 *   - comment-by-node-author: Comment by the author of the parent node.
 *   - comment-preview: When previewing a new or edited comment.
 *   The following applies only to viewers who are registered users:
 *   - comment-unpublished: An unpublished comment visible only to
 *     administrators.
 *   - comment-by-viewer: Comment by the user currently viewing the page.
 *   - comment-new: New comment since last the visit.
 * - $attributes: Array of additional HTML attributes that should be added to
 *   the wrapper element. Flatten with backdrop_attributes().
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * These two variables are provided for context:
 * - $comment: Full comment object.
 * - $node: Node entity the comments are attached to.
 *
 * @see template_preprocess()
 * @see template_preprocess_comment()
 * @see theme_comment()
 *
 * @ingroup themeable


<li class="media">
                                    <a href="#" class="pull-left">
                                        <img src="assets/img/1.png" alt="" class="img-circle">
                                    </a>
                                    <div class="media-body">
                                        <span class="text-muted pull-right">
                                            <small class="text-muted">30 min ago</small>
                                        </span>
                                        <strong class="text-success">@ Rexona Kumi</strong>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                            Lorem ipsum dolor sit amet, <a href="#"># consectetur adipiscing </a>.
                                        </p>
                                    </div>
                                </li>
 */
?>
<article class="media <?php print implode(' ', $classes); ?> clearfix"<?php print backdrop_attributes($attributes); ?>>
  <?php if($user_picture): ?>
  <div class="pull-left">
    <?php print $user_picture; ?>
  </div>
  <?php endif; ?>  
  <div class="media-body">
    <span class="text-muted pull-right">
        <small class="text-muted"><?php print $changed; ?></small>
    </span>
    <strong class="text-success"><?php print $author; ?></strong>
    <p class="content"<?php print backdrop_attributes($content_attributes); ?>>
      <?php
        // We hide the links now so that we can render them later.
        hide($content['links']);
        print render($content);
      ?>
      <?php if ($signature): ?>
      <div class="user-signature">
        <?php print $signature; ?>
      </div>
      <?php endif; ?>
    </p>

  <?php print render($content['links']) ?>
  </div>
</article>
