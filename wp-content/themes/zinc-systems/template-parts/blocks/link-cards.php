<?php
/**
 * Block: Link cards
 *
 * Heading above a joined row of linked cards; the hovered or focused card lifts out in teal.
 *
 * @var array $block
 * @var bool  $is_preview
 */
$tag   = get_field('tag');
$title = get_field('title');
$cards = get_field('cards') ?: array();
$uid   = 'lc-' . $block['id'];
?>

<section<?php echo zinc_block_attrs($block, 'b-link-cards', $is_preview); ?> aria-labelledby="<?php echo esc_attr($uid); ?>-title">
  <?php get_template_part('template-parts/blocks/partials/section-head', null, array(
    'tag'   => $tag,
    'title' => $title,
    'id'    => $uid . '-title',
  )); ?>

  <?php if ($cards) : ?>
    <ul class="b-link-cards__list" style="--cols: <?php echo count($cards); ?>">
      <?php foreach ($cards as $card) :
        $has_link = !empty($card['link']['url']);
        $element  = $has_link ? 'a' : 'div';
      ?>
        <li class="b-link-cards__item">
          <<?php echo $element; ?> class="b-link-cards__card"<?php if ($has_link) echo zinc_link_attrs($card['link']); ?>>
            <span class="b-link-cards__bg" aria-hidden="true">
              <img class="b-link-cards__pattern" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/patterns/star.png'); ?>" alt="">
            </span>
            <span class="b-link-cards__title">
              <?php echo esc_html($card['title']); ?>
              <?php if ($has_link) echo zinc_icon('arrow'); ?>
            </span>
            <?php if ($card['text']) : ?>
              <span class="b-link-cards__text"><?php echo esc_html($card['text']); ?></span>
            <?php endif; ?>
          </<?php echo $element; ?>>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
