<?php
/**
 * Header mega menu panel
 *
 * @var array $args id, menu (ACF mega_menu group)
 */
$menu    = $args['menu'];
$columns = !empty($menu['columns']) ? $menu['columns'] : array();
$feature = !empty($menu['show_feature']) ? $menu['feature'] : null;
$bar     = !empty($menu['show_bottom_bar']) ? $menu['bottom_bar'] : null;
?>

<div class="c-mega-menu" id="<?php echo esc_attr($args['id']); ?>" data-dropdown-panel>
  <div class="c-mega-menu__main">
    <?php if (!empty($menu['background_image']['url'])) : ?>
      <?php echo zinc_image($menu['background_image'], 'c-mega-menu__bg', ''); ?>
    <?php endif; ?>

    <?php foreach ($columns as $column) :
      $items    = !empty($column['items']) ? $column['items'] : array();
      $detailed = (bool) array_filter(wp_list_pluck($items, 'description'));
    ?>
      <div class="c-mega-menu__column c-mega-menu__column--<?php echo $detailed ? 'detailed' : 'icons'; ?>">
        <?php if (!empty($column['heading'])) : ?>
          <p class="c-mega-menu__heading"><?php echo esc_html($column['heading']); ?></p>
        <?php endif; ?>

        <?php if ($items) : ?>
          <ul class="c-mega-menu__list">
            <?php foreach ($items as $item) : if (empty($item['link']['url'])) continue; ?>
              <li>
                <a class="c-mega-menu__link"<?php echo zinc_link_attrs($item['link']); ?>>
                  <span class="c-mega-menu__link-main">
                    <?php if (!empty($item['icon']['url'])) : ?>
                      <span class="c-mega-menu__link-icon"><?php echo zinc_image($item['icon'], '', ''); ?></span>
                    <?php endif; ?>
                    <span class="c-mega-menu__link-text">
                      <span class="c-mega-menu__link-title"><?php echo esc_html($item['title'] ?: $item['link']['title']); ?></span>
                      <?php if (!empty($item['description'])) : ?>
                        <span class="c-mega-menu__link-desc"><?php echo esc_html($item['description']); ?></span>
                      <?php endif; ?>
                    </span>
                  </span>
                  <span class="c-mega-menu__arrow"><?php echo zinc_icon('arrow'); ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if (!empty($column['footer_link']['url'])) : ?>
          <a class="c-text-link"<?php echo zinc_link_attrs($column['footer_link']); ?>><?php echo esc_html($column['footer_link']['title']); ?><?php echo zinc_icon('arrow'); ?></a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <?php if ($feature) :
      $feature_link = !empty($feature['link']['url']) ? $feature['link'] : null;

      // A video plays in the pop-up player (assets/js/video-modal.js) when the image is clicked
      $video_type  = !empty($feature['video_type']) ? $feature['video_type'] : 'none';
      $video_attrs = '';
      if ($video_type === 'file' && !empty($feature['video_file']['url'])) {
        $video_attrs = ' data-video-src="' . esc_url($feature['video_file']['url']) . '"';
      } elseif (in_array($video_type, array('youtube', 'vimeo'), true) && ($embed = zinc_video_embed_url($feature['video_url']))) {
        $video_attrs = ' data-video-embed="' . esc_url($embed) . '"';
      }
      $video_label = sprintf(__('Play video: %s'), wp_strip_all_tags($feature['caption'] ?: $feature['title']));

      if ($video_attrs) {
        $media_tag   = 'button';
        $media_attrs = ' type="button" aria-haspopup="dialog" aria-label="' . esc_attr($video_label) . '" data-video-title="' . esc_attr($feature['title']) . '"' . $video_attrs;
      } elseif ($feature_link) {
        $media_tag   = 'a';
        $media_attrs = zinc_link_attrs($feature_link) . ' tabindex="-1" aria-hidden="true"';
      } else {
        $media_tag   = 'div';
        $media_attrs = '';
      }
    ?>
      <div class="c-mega-menu__feature">
        <?php if (!empty($feature['image']['url'])) : ?>
          <<?php echo $media_tag; ?> class="c-mega-menu__media"<?php echo $media_attrs; ?>>
            <?php echo zinc_image($feature['image'], 'c-mega-menu__media-img', ''); ?>
            <?php if ($video_attrs || !empty($feature['show_play'])) echo zinc_icon('play', 'c-mega-menu__play'); ?>
            <?php if (!empty($feature['caption'])) : ?>
              <span class="c-mega-menu__caption"><?php echo esc_html($feature['caption']); ?></span>
            <?php endif; ?>
          </<?php echo $media_tag; ?>>
        <?php endif; ?>

        <?php if (!empty($feature['title']) || !empty($feature['text'])) : ?>
          <div class="c-mega-menu__feature-copy">
            <?php if (!empty($feature['title'])) : ?>
              <p class="c-mega-menu__feature-title"><?php echo zinc_accent_title($feature['title']); ?></p>
            <?php endif; ?>
            <?php if (!empty($feature['text'])) : ?>
              <p class="c-mega-menu__feature-text"><?php echo esc_html($feature['text']); ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ($feature_link) : ?>
          <a class="c-text-link"<?php echo zinc_link_attrs($feature_link); ?>><?php echo esc_html($feature_link['title']); ?><?php echo zinc_icon('arrow'); ?></a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($bar && (!empty($bar['label']) || !empty($bar['links']))) : ?>
    <div class="c-mega-menu__bar">
      <?php if (!empty($bar['label'])) : ?>
        <p class="c-mega-menu__bar-label"><?php echo esc_html($bar['label']); ?></p>
      <?php endif; ?>
      <?php if (!empty($bar['links'])) : ?>
        <ul class="c-mega-menu__bar-links">
          <?php foreach ($bar['links'] as $bar_link) : if (empty($bar_link['link']['url'])) continue; ?>
            <li><a class="c-mega-menu__bar-link"<?php echo zinc_link_attrs($bar_link['link']); ?>><?php echo esc_html($bar_link['link']['title']); ?><?php echo zinc_icon('arrow'); ?></a></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
