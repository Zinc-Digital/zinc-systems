</main>

<?php
$cta_enabled    = get_field('footer_cta_enabled', 'option');
$cta            = get_field('footer_cta', 'option');
$has_cta        = $cta_enabled && !empty($cta['title']);
$logo           = get_field('footer_logo', 'option');
$phone          = get_field('footer_phone', 'option') ?: get_field('telephone', 'option');
$email          = get_field('footer_email', 'option') ?: get_field('email', 'option');
$office_label   = get_field('footer_office_label', 'option');
$address        = get_field('footer_address', 'option');
$columns        = get_field('footer_columns', 'option') ?: array();
$accreditations = get_field('footer_accreditations', 'option') ?: array();
$button         = get_field('footer_button', 'option');
$legal_links    = get_field('footer_legal_links', 'option') ?: array();
$copyright      = get_field('footer_copyright', 'option');
$tagline        = get_field('footer_tagline', 'option');
?>

<?php if ($has_cta) {
  get_template_part('template-parts/footer/footer-cta', null, array('cta' => $cta));
} ?>

<footer class="c-site-footer<?php if ($has_cta) echo ' c-site-footer--has-cta'; ?>">
  <div class="c-site-footer__inner">
    <div class="c-site-footer__top">
      <div class="c-site-footer__contact">
        <?php if (!empty($logo['url'])) : ?>
          <a class="c-site-footer__logo" href="<?php echo esc_url(home_url('/')); ?>">
            <?php echo zinc_image($logo, '', get_bloginfo('name') . ' home'); ?>
          </a>
        <?php endif; ?>

        <?php if ($phone || $email) : ?>
          <ul class="c-site-footer__contact-list">
            <?php if ($phone) : ?>
              <li><a class="c-site-footer__contact-link" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo zinc_icon('phone'); ?><?php echo esc_html($phone); ?></a></li>
            <?php endif; ?>
            <?php if ($email) : ?>
              <li><a class="c-site-footer__contact-link" href="mailto:<?php echo antispambot(sanitize_email($email)); ?>"><?php echo zinc_icon('email'); ?><?php echo antispambot(sanitize_email($email)); ?></a></li>
            <?php endif; ?>
          </ul>
        <?php endif; ?>

        <?php if ($office_label || $address) : ?>
          <address class="c-site-footer__address">
            <?php if ($office_label) : ?>
              <span class="c-site-footer__label"><?php echo esc_html($office_label); ?></span>
            <?php endif; ?>
            <?php echo nl2br(esc_html(trim((string) $address))); ?>
          </address>
        <?php endif; ?>
      </div>

      <div class="c-site-footer__main">
        <div class="c-site-footer__nav-wrap">
          <?php if ($columns) : ?>
            <nav class="c-site-footer__columns" aria-label="Footer">
              <?php foreach ($columns as $column) : if (empty($column['groups'])) continue; ?>
                <div class="c-site-footer__column">
                  <?php foreach ($column['groups'] as $group) : ?>
                    <div class="c-site-footer__group">
                      <?php if (!empty($group['heading'])) : ?>
                        <h2 class="c-site-footer__heading">
                          <?php if (!empty($group['heading_link']['url'])) : ?>
                            <a<?php echo zinc_link_attrs($group['heading_link']); ?>><?php echo esc_html($group['heading']); ?></a>
                          <?php else : ?>
                            <?php echo esc_html($group['heading']); ?>
                          <?php endif; ?>
                        </h2>
                      <?php endif; ?>
                      <?php if (!empty($group['links'])) : ?>
                        <ul class="c-site-footer__links">
                          <?php foreach ($group['links'] as $item) : if (empty($item['link']['url'])) continue; ?>
                            <li><a<?php echo zinc_link_attrs($item['link']); ?>><?php echo esc_html($item['link']['title']); ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endforeach; ?>
            </nav>
          <?php endif; ?>

          <?php if ($accreditations) : ?>
            <ul class="c-site-footer__accreditations">
              <?php foreach ($accreditations as $accreditation) : if (empty($accreditation['logo']['url'])) continue; ?>
                <li>
                  <?php if (!empty($accreditation['link']['url'])) : ?>
                    <a<?php echo zinc_link_attrs($accreditation['link']); ?>><?php echo zinc_image($accreditation['logo']); ?></a>
                  <?php else : ?>
                    <?php echo zinc_image($accreditation['logo']); ?>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="c-site-footer__aside">
          <?php if (!empty($button['url'])) : ?>
            <a class="c-btn c-btn--primary"<?php echo zinc_link_attrs($button); ?>><?php echo esc_html($button['title']); ?></a>
          <?php endif; ?>

          <?php if ($legal_links) : ?>
            <ul class="c-site-footer__links">
              <?php foreach ($legal_links as $item) : if (empty($item['link']['url'])) continue; ?>
                <li><a<?php echo zinc_link_attrs($item['link']); ?>><?php echo esc_html($item['link']['title']); ?></a></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <?php if ($copyright) : ?>
            <div class="c-site-footer__copyright">
              <?php foreach (array_filter(array_map('trim', explode("\n", $copyright))) as $line) : ?>
                <p><?php echo esc_html(str_replace('{year}', date('Y'), $line)); ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if ($tagline) : ?>
      <p class="c-site-footer__tagline"><?php echo esc_html($tagline); ?></p>
    <?php endif; ?>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
