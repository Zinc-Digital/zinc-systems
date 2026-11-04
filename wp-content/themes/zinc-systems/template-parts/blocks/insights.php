<?php
/**
 * Block: Insights
 *
 * Heading and "view all" link above a row of post cards that runs off the right edge and scrolls
 * sideways, with arrows (assets/js/blocks/carousel.js). Cards cycle through three colourways.
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag       = get_field('tag');
$title     = get_field('title');
$all_link  = get_field('all_link');
$label     = get_field('label');
$link_text = get_field('link_text') ?: __('Read article');
$uid       = 'in-' . $block['id'];

if (get_field('source') === 'manual') {
	$posts = get_field('posts') ?: array();
} else {
	$posts = get_posts(array(
		'post_type'           => 'post',
		'posts_per_page'      => (int) get_field('count') ?: 6,
		'ignore_sticky_posts' => true,
	));
}

// Background pattern for each colourway
$patterns = array('circles', 'circles', 'waves');
?>

<section<?php echo zinc_block_attrs($block, 'b-insights', $is_preview); ?> aria-labelledby="<?php echo esc_attr($uid); ?>-title" data-carousel="scroll">
  <div class="b-insights__inner">
    <div class="b-insights__head">
      <?php get_template_part('template-parts/blocks/partials/section-head', null, array(
        'tag'   => $tag,
        'title' => $title,
        'id'    => $uid . '-title',
      )); ?>
      <?php if (!empty($all_link['url'])) : ?>
        <a class="c-text-link b-insights__all"<?php echo zinc_link_attrs($all_link); ?>><?php echo esc_html($all_link['title']); ?><?php echo zinc_icon('arrow'); ?></a>
      <?php endif; ?>
    </div>

    <?php if ($posts) : ?>
      <ul class="b-insights__track" id="<?php echo esc_attr($uid); ?>-track" data-carousel-track>
        <?php foreach ($posts as $i => $insight) : $style = $i % 3; ?>
          <li class="b-insights__card b-insights__card--<?php echo $style + 1; ?>">
            <img class="b-insights__pattern" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/patterns/' . $patterns[$style] . '.png'); ?>" alt="" aria-hidden="true">
            <div class="b-insights__body">
              <div class="b-insights__meta">
                <?php if ($label) : ?><span class="b-insights__label"><?php echo esc_html($label); ?></span><?php endif; ?>
                <span class="b-insights__time"><?php echo esc_html(zinc_read_time($insight)); ?></span>
              </div>
              <h3 class="b-insights__title"><?php echo esc_html(get_the_title($insight)); ?></h3>
              <a class="b-insights__link" href="<?php echo esc_url(get_permalink($insight)); ?>">
                <?php echo esc_html($link_text); ?><?php echo zinc_icon('arrow'); ?>
                <span class="u-sr-only">: <?php echo esc_html(get_the_title($insight)); ?></span>
              </a>
            </div>
            <?php if (has_post_thumbnail($insight)) : ?>
              <div class="b-insights__media">
                <?php echo get_the_post_thumbnail($insight, 'large', array('class' => 'b-insights__image', 'alt' => '', 'loading' => 'lazy')); ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="c-arrows b-insights__arrows">
        <button class="c-arrows__btn c-arrows__btn--prev" type="button" aria-controls="<?php echo esc_attr($uid); ?>-track" data-carousel-prev disabled>
          <?php echo zinc_icon('arrow'); ?><span class="u-sr-only"><?php _e('Previous'); ?></span>
        </button>
        <button class="c-arrows__btn c-arrows__btn--next" type="button" aria-controls="<?php echo esc_attr($uid); ?>-track" data-carousel-next>
          <?php echo zinc_icon('arrow'); ?><span class="u-sr-only"><?php _e('Next'); ?></span>
        </button>
      </div>
    <?php elseif ($is_preview) : ?>
      <p class="b-insights__empty"><?php _e('No posts to show yet.'); ?></p>
    <?php endif; ?>
  </div>
</section>
