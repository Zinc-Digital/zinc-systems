<?php
/**
 * Block: Feature showcase
 *
 * Centred intro above a stack of large image cards: as the page scrolls, each card pins near the
 * top and the next slides up over it (CSS sticky; assets/js/blocks/stack.js keeps a card taller
 * than the window pinned by its bottom edge). Each card has a title, text and buttons over the
 * image, with floating stat and bar chart cards whose bars grow in when the card scrolls into view
 * (assets/js/blocks/reveal.js).
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag   = get_field('tag');
$title = get_field('title');
$text  = get_field('text');
$cards = get_field('cards') ?: array();
$uid   = 'fs-' . $block['id'];

// Bar colours, left to right
$bar_colours = array('#00363C', '#087F8C', 'rgba(8, 127, 140, 0.5)', '#36F1CD', '#C3FCF1');
?>

<section<?php echo zinc_block_attrs($block, 'b-feature-showcase', $is_preview); ?> aria-labelledby="<?php echo esc_attr($uid); ?>-title" data-stack>
  <div class="b-feature-showcase__intro">
    <?php get_template_part('template-parts/blocks/partials/section-head', null, array(
      'tag'   => $tag,
      'title' => $title,
      'id'    => $uid . '-title',
      'class' => 'c-section-head--center',
    )); ?>
    <?php if ($text) : ?>
      <p class="b-feature-showcase__text"><?php echo nl2br(esc_html($text), false); ?></p>
    <?php endif; ?>
  </div>

  <?php if ($cards) : ?>
    <div class="b-feature-showcase__cards">
      <?php foreach ($cards as $i => $card) :
        $stats  = !empty($card['stats']) ? $card['stats'] : array();
        $groups = !empty($card['chart_groups']) ? $card['chart_groups'] : array();
      ?>
        <article class="b-feature-showcase__card" style="--i: <?php echo $i; ?>" data-stack-card data-reveal data-reveal-repeat>
          <?php if (!empty($card['image']['url'])) : ?>
            <div class="b-feature-showcase__bg" aria-hidden="true">
              <?php echo zinc_image($card['image'], 'b-feature-showcase__image', ''); ?>
            </div>
          <?php endif; ?>

          <div class="b-feature-showcase__copy">
            <?php if ($card['title']) : ?>
              <h3 class="b-feature-showcase__card-title"><?php echo zinc_heading($card['title']); ?></h3>
            <?php endif; ?>

            <div class="b-feature-showcase__foot">
              <?php if ($card['text']) : ?>
                <p class="b-feature-showcase__card-text"><?php echo nl2br(esc_html($card['text']), false); ?></p>
              <?php endif; ?>
              <?php if (!empty($card['primary_button']['url']) || !empty($card['secondary_button']['url'])) : ?>
                <div class="b-feature-showcase__buttons">
                  <?php if (!empty($card['primary_button']['url'])) : ?>
                    <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($card['primary_button']); ?>><?php echo esc_html($card['primary_button']['title']); ?></a>
                  <?php endif; ?>
                  <?php if (!empty($card['secondary_button']['url'])) : ?>
                    <a class="c-btn c-btn--ghost"<?php echo zinc_link_attrs($card['secondary_button']); ?>><?php echo esc_html($card['secondary_button']['title']); ?></a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($card['widget_show']) && ($stats || $groups)) : ?>
            <?php // An illustration of the product, so it's hidden from assistive technology ?>
            <div class="b-feature-showcase__widget" aria-hidden="true" data-reveal-anchor>
              <?php if ($stats) : ?>
                <div class="b-feature-showcase__stats">
                  <?php foreach ($stats as $stat) : ?>
                    <div class="b-feature-showcase__stat">
                      <span class="b-feature-showcase__stat-value" data-count-to="<?php echo esc_attr($stat['value']); ?>"><?php echo esc_html($stat['value']); ?></span>
                      <span class="b-feature-showcase__stat-label"><?php echo esc_html($stat['label']); ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <?php if ($groups) : ?>
                <div class="b-feature-showcase__chart">
                  <div class="b-feature-showcase__chart-head">
                    <span class="b-feature-showcase__chart-title"><?php echo esc_html($card['chart_title']); ?></span>
                    <?php if ($card['chart_badge']) : ?>
                      <span class="b-feature-showcase__chart-badge"><?php echo esc_html($card['chart_badge']); ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="b-feature-showcase__groups">
                    <?php foreach ($groups as $g => $group) :
                      $values = array_slice(array_map('floatval', array_filter(array_map('trim', explode(',', (string) $group['values'])), 'strlen')), 0, 5);
                    ?>
                      <div class="b-feature-showcase__group">
                        <div class="b-feature-showcase__bars">
                          <?php foreach ($values as $v => $value) : ?>
                            <span class="b-feature-showcase__bar" style="<?php echo esc_attr(sprintf('--h:%s%%;--d:%ss;background:%s', max(0, min(100, $value)), round(($g * 5 + $v) * 0.06, 2), $bar_colours[$v])); ?>"></span>
                          <?php endforeach; ?>
                        </div>
                        <span class="b-feature-showcase__group-label"><?php echo esc_html($group['label']); ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
