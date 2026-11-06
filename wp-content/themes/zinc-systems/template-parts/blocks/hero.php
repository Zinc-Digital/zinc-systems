<?php
/**
 * Block: Hero
 *
 * Pinned while you scroll: the copy fades back, the capability chips gather in behind the
 * product UI, and the UI rises to the middle of the screen and builds itself. Animated by
 * assets/js/blocks/hero.js; laid out on a 1440px design stage that scales with the window.
 *
 * @var array $block
 * @var bool  $is_preview
 */
$headline         = get_field('headline');
$highlight        = get_field('headline_highlight');
$text             = get_field('text');
$primary_button   = get_field('primary_button');
$secondary_button = get_field('secondary_button');
$chips            = get_field('chips') ?: array();
$ui_type          = get_field('ui_type') ?: 'dashboard';
$ui_image         = get_field('ui_image');
$demo_show        = get_field('demo_show');
$demo_title       = get_field('demo_title');
$demo_highlight   = get_field('demo_highlight');
$demo_note        = get_field('demo_note');
$demo_primary     = get_field('demo_primary');
$demo_secondary   = get_field('demo_secondary');
$shapes           = get_field('background_shapes');
$logos_label      = get_field('logos_label');
$logos            = array_values(array_filter(get_field('logos') ?: array(), function ($l) { return !empty($l['image']['url']); }));
$logos_slider     = count($logos) > 5;

$shapes_url = !empty($shapes['url']) ? $shapes['url'] : get_template_directory_uri() . '/assets/images/hero/shapes.svg';
$has_image  = $ui_type === 'image' && !empty($ui_image['url']);

$headline_lines = array_filter(array_map('trim', explode("\n", (string) $headline)), 'strlen');

$demo_title_html = esc_html($demo_title);
if ($demo_highlight && strpos($demo_title, $demo_highlight) !== false) {
	$demo_title_html = preg_replace('/' . preg_quote(esc_html($demo_highlight), '/') . '/', '<b>$0</b>', $demo_title_html, 1);
}

$classes = 'b-hero';
if ($is_preview) {
	$classes .= ' is-preview';
}
if (!empty($block['className'])) {
	$classes .= ' ' . $block['className'];
}
?>

<div class="<?php echo esc_attr($classes); ?>"<?php if (!empty($block['anchor'])) echo ' id="' . esc_attr($block['anchor']) . '"'; ?> data-hero>
  <section class="b-hero__scroll" data-hero-scroll>
    <div class="b-hero__pin" data-hero-pin>
      <div class="b-hero__stage">
        <div class="b-hero__bg" data-hero-bg>
          <img class="b-hero__shapes" src="<?php echo esc_url($shapes_url); ?>" alt="" decoding="async">
        </div>

        <div class="b-hero__copy" data-hero-copy>
          <?php if ($headline_lines || $highlight) : ?>
            <h1 class="b-hero__headline">
              <?php echo implode('<br>', array_map('esc_html', $headline_lines)); ?>
              <?php if ($highlight) : ?><span class="b-hero__highlight"><?php echo esc_html($highlight); ?></span><?php endif; ?>
            </h1>
          <?php endif; ?>

          <div class="b-hero__sub">
            <?php if ($text) : ?>
              <p class="b-hero__text"><?php echo esc_html($text); ?></p>
            <?php endif; ?>

            <?php if (!empty($primary_button['url']) || !empty($secondary_button['url'])) : ?>
              <div class="b-hero__ctas">
                <?php if (!empty($primary_button['url'])) : ?>
                  <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($primary_button); ?>><?php echo esc_html($primary_button['title']); ?></a>
                <?php endif; ?>
                <?php if (!empty($secondary_button['url'])) : ?>
                  <a class="c-btn c-btn--ghost"<?php echo zinc_link_attrs($secondary_button); ?>><?php echo esc_html($secondary_button['title']); ?></a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($chips) : ?>
          <ul class="b-hero__chips" data-hero-chips>
            <?php foreach ($chips as $chip) :
              $style = sprintf('left:%spx;top:%spx;--bob:%ss;--bd:%ss', (float) $chip['x'], (float) $chip['y'], (float) ($chip['bob'] ?: 6), (float) $chip['delay']);
            ?>
              <li class="b-hero__chip" style="<?php echo esc_attr($style); ?>" data-depth="<?php echo esc_attr((float) ($chip['depth'] ?: 1)); ?>"<?php if (!empty($chip['faint'])) echo ' data-faint'; ?>>
                <span class="b-hero__chip-in">
                  <?php if (!empty($chip['icon']['url'])) echo zinc_image($chip['icon'], '', '', false); ?>
                  <?php echo esc_html($chip['label']); ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <div class="b-hero__ui<?php if ($has_image) echo ' b-hero__ui--image'; ?>" data-hero-ui>
          <?php if ($has_image) : ?>
            <?php echo zinc_image($ui_image, 'b-hero__ui-image', null, false); ?>
          <?php else : ?>
            <?php get_template_part('template-parts/blocks/partials/hero-dashboard', null, array(
              'dashboard' => zinc_hero_dashboard_data(get_field('dashboard')),
            )); ?>
          <?php endif; ?>

          <?php if ($demo_show && ($demo_title || !empty($demo_primary['url']))) : ?>
            <div class="b-hero__demo" data-hero-demo>
              <div class="b-hero__demo-inner">
                <?php if ($demo_title) : ?>
                  <p class="b-hero__demo-title"><?php echo $demo_title_html; ?></p>
                <?php endif; ?>
                <?php if ($demo_note) : ?>
                  <p class="b-hero__demo-note"><?php echo esc_html($demo_note); ?></p>
                <?php endif; ?>
                <?php if (!empty($demo_primary['url']) || !empty($demo_secondary['url'])) : ?>
                  <div class="b-hero__demo-ctas">
                    <?php if (!empty($demo_primary['url'])) : ?>
                      <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($demo_primary); ?>><?php echo esc_html($demo_primary['title']); ?></a>
                    <?php endif; ?>
                    <?php if (!empty($demo_secondary['url'])) : ?>
                      <a class="c-btn c-btn--ghost"<?php echo zinc_link_attrs($demo_secondary); ?>><?php echo esc_html($demo_secondary['title']); ?></a>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <?php if ($logos) : ?>
    <section class="b-hero__logos<?php if ($logos_slider) echo ' b-hero__logos--slider'; ?>" aria-label="<?php echo esc_attr($logos_label ?: 'Trusted by'); ?>">
      <div class="b-hero__logos-stage">
        <div class="b-hero__logos-strip">
          <div class="b-hero__logos-row">
            <?php if ($logos_label) : ?>
              <p class="b-hero__logos-label"><?php echo esc_html($logos_label); ?></p>
            <?php endif; ?>
            <div class="b-hero__logos-list">
              <ul class="b-hero__logos-track" style="--count: <?php echo count($logos); ?>">
                <?php
                // The slider scrolls a second copy in behind the first, so the loop is seamless
                foreach ($logos_slider ? array(false, true) : array(false) as $copy) :
                  foreach ($logos as $logo) :
                ?>
                  <li class="b-hero__logo"<?php if ($copy) echo ' aria-hidden="true"'; ?>>
                    <?php if (!empty($logo['link']['url'])) : ?>
                      <a<?php echo zinc_link_attrs($logo['link']); ?><?php if ($copy) echo ' tabindex="-1"'; ?>><?php echo zinc_image($logo['image']); ?></a>
                    <?php else : ?>
                      <?php echo zinc_image($logo['image'], '', $copy ? '' : null); ?>
                    <?php endif; ?>
                  </li>
                <?php endforeach; endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>
