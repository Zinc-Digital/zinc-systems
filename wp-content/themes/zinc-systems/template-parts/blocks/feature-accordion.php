<?php
/**
 * Block: Feature accordion
 *
 * Square image beside a heading and an accordion; one item is open at a time, and an item with
 * its own image swaps it in (assets/js/blocks/feature-accordion.js).
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag   = get_field('tag');
$title = get_field('title');
$image = get_field('image');
$items = get_field('items') ?: array();
$uid   = 'fa-' . $block['id'];
?>

<section<?php echo zinc_block_attrs($block, 'b-feature-accordion', $is_preview); ?> aria-labelledby="<?php echo esc_attr($uid); ?>-title" data-feature-accordion>
  <div class="b-feature-accordion__inner">
    <div class="b-feature-accordion__media">
      <?php if (!empty($image['url'])) echo zinc_image($image, 'b-feature-accordion__image is-active'); ?>
      <?php foreach ($items as $i => $item) : if (empty($item['image']['url'])) continue; ?>
        <?php echo str_replace('<img ', '<img data-item="' . $i . '" ', zinc_image($item['image'], 'b-feature-accordion__image')); ?>
      <?php endforeach; ?>
    </div>

    <div class="b-feature-accordion__content">
      <?php get_template_part('template-parts/blocks/partials/section-head', null, array(
        'tag'   => $tag,
        'title' => $title,
        'id'    => $uid . '-title',
      )); ?>

      <?php if ($items) : ?>
        <div class="b-feature-accordion__items">
          <?php foreach ($items as $i => $item) : $open = $i === 0; ?>
            <div class="b-feature-accordion__item<?php if ($open) echo ' is-open'; ?>" data-item="<?php echo $i; ?>">
              <h3 class="b-feature-accordion__heading">
                <button class="b-feature-accordion__toggle" type="button" id="<?php echo esc_attr($uid . '-toggle-' . $i); ?>" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($uid . '-panel-' . $i); ?>">
                  <?php echo esc_html($item['title']); ?>
                </button>
              </h3>
              <div class="b-feature-accordion__panel" id="<?php echo esc_attr($uid . '-panel-' . $i); ?>" role="region" aria-labelledby="<?php echo esc_attr($uid . '-toggle-' . $i); ?>"<?php if (!$open) echo ' inert'; ?>>
                <div class="b-feature-accordion__panel-inner">
                  <?php if ($item['text']) : ?>
                    <p class="b-feature-accordion__text"><?php echo nl2br(esc_html($item['text']), false); ?></p>
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
