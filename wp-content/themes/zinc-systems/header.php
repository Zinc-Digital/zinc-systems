<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php wp_head(); ?>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!--Replace this with theme font if using Google
		<link href="https://fonts.googleapis.com/css2?family=Funnel+Display:wght@300..800&display=swap" rel="stylesheet">
	-->

  <script src="https://kit.fontawesome.com/ef400fc0cd.js" crossorigin="anonymous"></script>

</head>

<body <?php body_class(); ?>>

  <?php
  $logo               = get_field('header_logo', 'option') ?: get_field('logo', 'option');
  $nav_items          = get_field('header_nav', 'option') ?: array();
  $show_search        = get_field('header_show_search', 'option');
  $search_placeholder = get_field('header_search_placeholder', 'option') ?: 'Search';
  $button             = get_field('header_button', 'option');
  $region_label       = get_field('header_region_label', 'option');
  $translator         = zinc_gtranslate_languages();
  ?>

  <a class="c-skip-link" href="#main-content">Skip to content</a>

  <header class="c-site-header" data-site-header>
    <div class="c-site-header__bar">
      <a class="c-site-header__logo" href="<?php echo esc_url(home_url('/')); ?>">
        <?php if (!empty($logo['url'])) : ?>
          <?php echo zinc_image($logo, 'c-site-header__logo-img', get_bloginfo('name') . ' home', false); ?>
        <?php else : ?>
          <?php bloginfo('name'); ?>
        <?php endif; ?>
      </a>

      <div class="c-site-header__panel" id="site-header-panel" data-header-panel>
        <?php if ($nav_items) : ?>
          <nav class="c-site-header__nav" aria-label="Main">
            <ul class="c-site-header__menu">
              <?php foreach ($nav_items as $i => $item) :
                $has_link = !empty($item['link']['url']);
                $has_mega = !empty($item['has_mega_menu']) && !empty($item['mega_menu']);
                $mega_id  = 'mega-menu-' . ($i + 1);
              ?>
                <li class="c-site-header__item<?php if ($has_mega) echo ' has-mega-menu'; ?>"<?php if ($has_mega) echo ' data-dropdown'; ?>>
                  <div class="c-site-header__item-row">
                    <?php if ($has_link) : ?>
                      <a class="c-site-header__link"<?php echo zinc_link_attrs($item['link']); ?>><?php echo esc_html($item['label']); ?></a>
                    <?php elseif (!$has_mega) : ?>
                      <span class="c-site-header__link"><?php echo esc_html($item['label']); ?></span>
                    <?php endif; ?>

                    <?php if ($has_mega) : ?>
                      <button type="button" class="c-site-header__toggle" aria-expanded="false" aria-controls="<?php echo esc_attr($mega_id); ?>" data-dropdown-toggle>
                        <?php if ($has_link) : ?>
                          <span class="u-sr-only"><?php echo esc_html($item['label']); ?> menu</span>
                        <?php else : ?>
                          <span class="c-site-header__link"><?php echo esc_html($item['label']); ?></span>
                        <?php endif; ?>
                        <?php echo zinc_icon('chevron'); ?>
                      </button>
                    <?php endif; ?>
                  </div>

                  <?php if ($has_mega) {
                    get_template_part('template-parts/header/mega-menu', null, array(
                      'id'   => $mega_id,
                      'menu' => $item['mega_menu'],
                    ));
                  } ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </nav>
        <?php endif; ?>

        <div class="c-site-header__actions">
          <?php if ($show_search) : ?>
            <form class="c-site-header__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
              <label class="u-sr-only" for="site-header-search">Search the site</label>
              <?php echo zinc_icon('search'); ?>
              <input id="site-header-search" class="c-site-header__search-input" type="search" name="s" placeholder="<?php echo esc_attr($search_placeholder); ?>" value="<?php echo esc_attr(get_search_query()); ?>">
            </form>
          <?php endif; ?>

          <?php if (!empty($button['url'])) : ?>
            <a class="c-btn c-btn--primary c-site-header__button"<?php echo zinc_link_attrs($button); ?>><?php echo esc_html($button['title']); ?></a>
          <?php endif; ?>

          <?php if ($region_label && $translator) : ?>
            <div class="c-site-header__region notranslate" data-dropdown data-language-switcher data-default-lang="<?php echo esc_attr($translator['default']); ?>">
              <button type="button" class="c-site-header__region-toggle" aria-expanded="false" aria-controls="site-header-languages" data-dropdown-toggle>
                <span class="u-sr-only">Language:</span>
                <span data-language-label data-default-label="<?php echo esc_attr($region_label); ?>"><?php echo esc_html($region_label); ?></span>
                <?php echo zinc_icon('chevron'); ?>
              </button>
              <ul class="c-site-header__region-list" id="site-header-languages" data-dropdown-panel>
                <?php foreach ($translator['languages'] as $lang) : ?>
                  <li><?php echo do_shortcode('[gt-link lang="' . esc_attr($lang) . '" widget_look="lang_names"]'); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php elseif ($region_label) : ?>
            <div class="c-site-header__region">
              <span class="c-site-header__region-toggle"><?php echo esc_html($region_label); ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <button type="button" class="c-site-header__menu-toggle" aria-expanded="false" aria-controls="site-header-panel" data-menu-toggle>
        <span class="u-sr-only" data-menu-toggle-label>Open menu</span>
        <?php echo zinc_icon('menu', 'c-site-header__menu-icon'); ?>
        <?php echo zinc_icon('close', 'c-site-header__close-icon'); ?>
      </button>
    </div>
  </header>

  <div id="main-content" tabindex="-1"></div>
