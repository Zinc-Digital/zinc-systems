<?php
/**
 * Block heading: tag pill above a large title
 *
 * @var array $args tag, title, id (for aria-labelledby), class, light (white tag for dark backgrounds)
 */
$args = wp_parse_args($args, array('tag' => '', 'title' => '', 'id' => '', 'class' => '', 'light' => false));
?>

<div class="c-section-head<?php if ($args['class']) echo ' ' . esc_attr($args['class']); ?>">
  <?php if ($args['tag']) : ?>
    <p class="c-tag<?php if ($args['light']) echo ' c-tag--light'; ?>"><?php echo esc_html($args['tag']); ?></p>
  <?php endif; ?>
  <?php if ($args['title']) : ?>
    <h2 class="c-section-head__title"<?php if ($args['id']) echo ' id="' . esc_attr($args['id']) . '"'; ?>><?php echo zinc_heading($args['title']); ?></h2>
  <?php endif; ?>
</div>
