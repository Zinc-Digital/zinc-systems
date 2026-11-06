<?php
/**
 * Block: Case studies
 *
 * Grey card with a quote, attribution and results beside an image. With more than one slide,
 * arrows step between them (assets/js/blocks/carousel.js); the slides cross-fade.
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag      = get_field('tag');
$all_link = get_field('all_link');
$slides   = array_values(array_filter(get_field('slides') ?: array(), function ($s) { return !empty($s['quote']); }));
$uid      = 'cs-' . $block['id'];
$multiple = count($slides) > 1;
?>

<section<?php echo zinc_block_attrs($block, 'b-case-studies', $is_preview); ?> aria-roledescription="carousel" aria-label="<?php echo esc_attr($tag ?: __('Case studies')); ?>"<?php if ($multiple) echo ' data-carousel="fade"'; ?>>
  <div class="b-case-studies__inner">
    <div class="b-case-studies__card">
      <div class="b-case-studies__top">
        <?php if ($tag) : ?>
          <p class="c-tag"><?php echo esc_html($tag); ?></p>
        <?php endif; ?>
        <?php if ($multiple) : ?>
          <div class="c-arrows">
            <button class="c-arrows__btn c-arrows__btn--prev" type="button" aria-controls="<?php echo esc_attr($uid); ?>-slides" data-carousel-prev disabled>
              <?php echo zinc_icon('arrow'); ?><span class="u-sr-only"><?php _e('Previous case study'); ?></span>
            </button>
            <button class="c-arrows__btn c-arrows__btn--next" type="button" aria-controls="<?php echo esc_attr($uid); ?>-slides" data-carousel-next>
              <?php echo zinc_icon('arrow'); ?><span class="u-sr-only"><?php _e('Next case study'); ?></span>
            </button>
          </div>
        <?php endif; ?>
      </div>

      <div class="b-case-studies__slides" id="<?php echo esc_attr($uid); ?>-slides" aria-live="polite">
        <?php foreach ($slides as $i => $slide) : $active = $i === 0; ?>
          <div class="b-case-studies__slide<?php if ($active) echo ' is-active'; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr(sprintf(__('%1$d of %2$d'), $i + 1, count($slides))); ?>" data-slide<?php if (!$active) echo ' inert aria-hidden="true"'; ?>>
            <div class="b-case-studies__story">
              <div class="b-case-studies__logo">
                <?php if (!empty($slide['logo']['url'])) echo zinc_image($slide['logo']); ?>
              </div>
              <blockquote class="b-case-studies__quote">
                <p><?php echo nl2br(esc_html($slide['quote']), false); ?></p>
                <?php if ($slide['author']) : ?>
                  <footer class="b-case-studies__author">- <?php echo esc_html($slide['author']); ?></footer>
                <?php endif; ?>
              </blockquote>
            </div>

            <div class="b-case-studies__results">
              <?php if (!empty($slide['stats'])) : ?>
                <ul class="b-case-studies__stats" data-reveal data-reveal-repeat data-reveal-anchor>
                  <?php foreach ($slide['stats'] as $stat) : ?>
                    <li class="b-case-studies__stat">
                      <span class="b-case-studies__stat-value">
                        <span aria-hidden="true" data-count-to="<?php echo esc_attr($stat['value']); ?>"><?php echo esc_html($stat['value']); ?></span>
                        <span class="u-sr-only"><?php echo esc_html($stat['value']); ?></span>
                      </span>
                      <span class="b-case-studies__stat-label"><?php echo esc_html($stat['label']); ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>

              <?php if (!empty($slide['button']['url']) || !empty($all_link['url'])) : ?>
                <div class="b-case-studies__links">
                  <?php if (!empty($slide['button']['url'])) : ?>
                    <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($slide['button']); ?>><?php echo esc_html($slide['button']['title']); ?></a>
                  <?php endif; ?>
                  <?php if (!empty($all_link['url'])) : ?>
                    <a class="c-text-link"<?php echo zinc_link_attrs($all_link); ?>><?php echo esc_html($all_link['title']); ?><?php echo zinc_icon('arrow'); ?></a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="b-case-studies__media" aria-hidden="true">
      <?php foreach ($slides as $i => $slide) : if (empty($slide['image']['url'])) continue; ?>
        <?php echo str_replace('<img ', '<img data-slide-image="' . $i . '" ', zinc_image($slide['image'], 'b-case-studies__image' . ($i === 0 ? ' is-active' : ''), '')); ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
