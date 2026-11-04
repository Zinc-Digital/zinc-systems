<?php
/**
 * Call to action panel that overlaps the top of the footer
 *
 * @var array $args cta (ACF footer_cta group)
 */
$cta = $args['cta'];
?>

<section class="c-footer-cta" aria-labelledby="footer-cta-title">
  <div class="c-footer-cta__panel">
    <?php if (!empty($cta['background_image']['url'])) : ?>
      <?php echo zinc_image($cta['background_image'], 'c-footer-cta__bg', ''); ?>
    <?php endif; ?>

    <div class="c-footer-cta__content">
      <h2 class="c-footer-cta__title" id="footer-cta-title"><?php echo esc_html($cta['title']); ?></h2>

      <?php if (!empty($cta['text'])) : ?>
        <p class="c-footer-cta__text"><?php echo esc_html($cta['text']); ?></p>
      <?php endif; ?>

      <?php if (!empty($cta['primary_button']['url']) || !empty($cta['secondary_button']['url'])) : ?>
        <div class="c-footer-cta__buttons">
          <?php if (!empty($cta['primary_button']['url'])) : ?>
            <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($cta['primary_button']); ?>><?php echo esc_html($cta['primary_button']['title']); ?></a>
          <?php endif; ?>
          <?php if (!empty($cta['secondary_button']['url'])) : ?>
            <a class="c-btn c-btn--outline"<?php echo zinc_link_attrs($cta['secondary_button']); ?>><?php echo esc_html($cta['secondary_button']['title']); ?></a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
