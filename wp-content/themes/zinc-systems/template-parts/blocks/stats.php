<?php
/**
 * Block: Stats
 *
 * Teal band with a heading and large numbers that count up when scrolled into view
 * (assets/js/blocks/reveal.js). Each value is shown in full for assistive technology.
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag   = get_field('tag');
$title = get_field('title');
$stats = get_field('stats') ?: array();
$uid   = 'st-' . $block['id'];
?>

<section<?php echo zinc_block_attrs($block, 'b-stats', $is_preview); ?> aria-labelledby="<?php echo esc_attr($uid); ?>-title" data-reveal>
  <img class="b-stats__pattern" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/patterns/arch.png'); ?>" alt="" aria-hidden="true">

  <div class="b-stats__inner">
    <?php get_template_part('template-parts/blocks/partials/section-head', null, array(
      'tag'   => $tag,
      'title' => $title,
      'id'    => $uid . '-title',
      'light' => true,
    )); ?>

    <?php if ($stats) : ?>
      <ul class="b-stats__list">
        <?php foreach ($stats as $stat) : ?>
          <li class="b-stats__item">
            <span class="b-stats__value">
              <span aria-hidden="true" data-count-to="<?php echo esc_attr($stat['value']); ?>"><?php echo esc_html($stat['value']); ?></span>
              <span class="u-sr-only"><?php echo esc_html($stat['value']); ?></span>
            </span>
            <span class="b-stats__label"><?php echo esc_html($stat['label']); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>
