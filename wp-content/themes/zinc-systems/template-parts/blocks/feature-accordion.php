<?php
/**
 * Block: Feature accordion
 *
 * Square image beside a heading and an accordion; one item is open at a time, and an item with
 * its own image swaps it in (assets/js/blocks/feature-accordion.js). Each title can show its item's
 * icon. White, grey or teal background, with an
 * optional faint shape; with intro text the heading sits in a row above both columns. Items can list
 * what they include, under their text (switchable for the whole block).
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag   = get_field('tag');
$title = get_field('title');
$text  = get_field('show_text') !== false ? get_field('text') : ''; // on unless switched off
$image = get_field('image');
$items = get_field('items') ?: array();
$uid   = 'fa-' . $block['id'];

$show_icons  = get_field('show_icons') !== false; // on unless switched off (blocks saved before the option have none)
$background  = in_array(get_field('background'), array('grey', 'teal'), true) ? get_field('background') : 'white';
$show_shape  = (bool) get_field('show_shape');
$show_incl   = get_field('show_includes') !== false; // on unless switched off
$incl_label  = get_field('includes_label') ?: __('Includes');

// An item's includes: its repeater rows, or text saved before Includes became a repeater (split on the dots)
$includes = function ($item, $i) use ($block) {
	$rows = !empty($item['include_items']) ? wp_list_pluck($item['include_items'], 'label') : array();
	if (!$rows && !empty($block['data']['items_' . $i . '_includes']) && is_string($block['data']['items_' . $i . '_includes'])) {
		$rows = explode('·', $block['data']['items_' . $i . '_includes']);
	}
	return array_values(array_filter(array_map('trim', $rows), 'strlen'));
};

$classes = 'b-feature-accordion b-feature-accordion--' . $background;
if ($text) {
	$classes .= ' b-feature-accordion--head-row';
}

$head = array(
	'tag'   => $tag,
	'title' => $title,
	'id'    => $uid . '-title',
	'light' => $background === 'teal',
);
?>

<section<?php echo zinc_block_attrs($block, $classes, $is_preview); ?> aria-labelledby="<?php echo esc_attr($uid); ?>-title" data-feature-accordion>
  <?php if ($show_shape) : ?>
    <span class="b-feature-accordion__shape" aria-hidden="true" style="--shape: url('<?php echo esc_url(get_template_directory_uri() . '/assets/images/patterns/quarter-circle.png'); ?>')"></span>
  <?php endif; ?>

  <?php if ($text) : ?>
    <div class="b-feature-accordion__head">
      <?php get_template_part('template-parts/blocks/partials/section-head', null, $head); ?>
      <p class="b-feature-accordion__intro"><?php echo nl2br(esc_html($text), false); ?></p>
    </div>
  <?php endif; ?>

  <div class="b-feature-accordion__inner">
    <div class="b-feature-accordion__media">
      <?php if (!empty($image['url'])) echo zinc_image($image, 'b-feature-accordion__image is-active'); ?>
      <?php foreach ($items as $i => $item) : if (empty($item['image']['url'])) continue; ?>
        <?php echo str_replace('<img ', '<img data-item="' . $i . '" ', zinc_image($item['image'], 'b-feature-accordion__image')); ?>
      <?php endforeach; ?>
    </div>

    <div class="b-feature-accordion__content">
      <?php if (!$text) get_template_part('template-parts/blocks/partials/section-head', null, $head); ?>

      <?php if ($items) : ?>
        <div class="b-feature-accordion__items">
          <?php foreach ($items as $i => $item) :
            $open     = $i === 0;
            $has_icon = !empty($item['icon']['url']);
          ?>
            <div class="b-feature-accordion__item<?php if ($open) echo ' is-open'; ?>" data-item="<?php echo $i; ?>">
              <h3 class="b-feature-accordion__heading">
                <button class="b-feature-accordion__toggle" type="button" id="<?php echo esc_attr($uid . '-toggle-' . $i); ?>" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($uid . '-panel-' . $i); ?>">
                  <?php if ($show_icons && $has_icon) : ?>
                    <span class="b-feature-accordion__icon"><?php echo zinc_image($item['icon'], '', '', false); ?></span>
                  <?php endif; ?>
                  <span class="b-feature-accordion__label"><?php echo esc_html($item['title']); ?></span>
                </button>
              </h3>
              <div class="b-feature-accordion__panel" id="<?php echo esc_attr($uid . '-panel-' . $i); ?>" role="region" aria-labelledby="<?php echo esc_attr($uid . '-toggle-' . $i); ?>"<?php if (!$open) echo ' inert'; ?>>
                <div class="b-feature-accordion__panel-inner">
                  <?php if ($item['text']) : ?>
                    <p class="b-feature-accordion__text"><?php echo nl2br(esc_html($item['text']), false); ?></p>
                  <?php endif; ?>
                  <?php $item_includes = $show_incl ? $includes($item, $i) : array(); ?>
                  <?php if ($item_includes) : ?>
                    <div class="b-feature-accordion__includes">
                      <p class="b-feature-accordion__includes-label" id="<?php echo esc_attr($uid . '-includes-' . $i); ?>"><?php echo esc_html($incl_label); ?></p>
                      <ul class="b-feature-accordion__includes-list" aria-labelledby="<?php echo esc_attr($uid . '-includes-' . $i); ?>">
                        <?php foreach ($item_includes as $include) : ?>
                          <li><?php echo esc_html($include); ?></li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  <?php endif; ?>
                  <?php if (!empty($item['button']['url'])) : ?>
                    <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($item['button']); ?>><?php echo esc_html($item['button']['title']); ?></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
