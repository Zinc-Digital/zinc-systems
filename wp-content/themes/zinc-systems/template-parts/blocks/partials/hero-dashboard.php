<?php
/**
 * Hero block: the product UI, rebuilt as markup so it can animate (1240 × 828 internal units).
 * Content comes from the block's Dashboard fields (see zinc_hero_dashboard_data()).
 * It's an illustration, so it's hidden from assistive technology.
 *
 * @var array $args dashboard
 */
$d = $args['dashboard'];

// KPI icons: background colour and glyph
$icons = array(
	'assets'      => array('#e3f1f6', '<svg width="16" height="14" viewBox="0 0 16 14" fill="#46a3c7"><path d="M1 3h14v10H1zM3 1h10v1.5H3z"/><path d="M1 7h14" stroke="#e3f1f6" stroke-width="1.2"/></svg>'),
	'checked_out' => array('#e2f7e6', '<svg width="18" height="14" viewBox="0 0 18 14" fill="#38995a"><circle cx="6" cy="4" r="3"/><path d="M0 13c0-3.3 2.7-5 6-5s6 1.7 6 5z"/><path d="m11 6 2 2 4-4" stroke="#38995a" stroke-width="1.6" fill="none"/></svg>'),
	'overdue'     => array('#fbe4da', '<svg width="14" height="14" viewBox="0 0 14 14"><circle cx="7" cy="7" r="6.5" fill="#d2532d"/><path d="M7 3.5V7l2.2 1.4" stroke="#fff" stroke-width="1.4" fill="none"/></svg>'),
	'available'   => array('#ececec', '<svg width="16" height="13" viewBox="0 0 16 13" fill="#45494d"><path d="M1 3 8 0l7 3-7 3zM1 4l7 3v6l-7-3zm14 0-7 3v6l7-3z"/></svg>'),
	'returns'     => array('#e7f2f8', '<svg width="14" height="15" viewBox="0 0 14 15" fill="#3d92c4"><path d="M1 3h12v11H1zM3 1h2v3H3zm6 0h2v3H9z"/><path d="m4 9 2 2 4-4" stroke="#e7f2f8" stroke-width="1.4" fill="none"/></svg>'),
	'deactivated' => array('#fdf2dc', '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="#e8a136" stroke-width="1.8"><circle cx="7.5" cy="7.5" r="6"/><path d="m3.3 3.3 8.4 8.4"/></svg>'),
);
$icon = function ($name) use ($icons) {
	$i = isset($icons[$name]) ? $icons[$name] : $icons['assets'];
	return '<div class="b-hero-dash__k-i" style="background:' . $i[0] . '">' . $i[1] . '</div>';
};

// Donut segments: each one's arc (in degrees) from its share of the total
$segments = array_values(array_filter($d['segments'], function ($s) { return !empty($s['label']); }));
foreach ($segments as &$segment) {
	$segment['colour'] = sanitize_hex_color($segment['colour']) ?: '#4CA5DA';
}
unset($segment);
$total    = array_sum(array_map(function ($s) { return max(0, (float) $s['value']); }, $segments));
$arcs     = array();
$from     = 0;
foreach ($segments as $segment) {
	$to     = $total > 0 ? $from + 360 * max(0, (float) $segment['value']) / $total : $from;
	$arcs[] = array('from' => round($from, 2), 'to' => round($to, 2), 'colour' => $segment['colour']);
	$from   = $to;
}

// Line chart points, drawn by hero.js
$points = array_values(array_map(function ($p) {
	return array('label' => (string) $p['label'], 'value' => max(0, (float) $p['value']));
}, array_filter($d['points'], function ($p) { return isset($p['label']) && $p['label'] !== ''; })));

$kpis = array_slice($d['kpis'], 0, 5);
$card_delay = 0.05;
?>

<div class="b-hero-dash" aria-hidden="true" data-hero-dash>
  <div class="b-hero-dash__side">
    <svg class="b-hero-dash__mark" viewBox="0 0 45 50" fill="none" stroke="#3ea9cc" stroke-width="2.4" stroke-linejoin="round"><path d="M22.5 2 42 13.3v23.4L22.5 48 3 36.7V13.3Z"/><path d="M22.5 2 12 30M42 13.3 14 18M42 36.7 22 18M22.5 48 33 20M3 36.7l28-4.7M3 13.3l20 18.7"/></svg>
    <div class="b-hero-dash__rule" style="top:90px"></div>
    <div class="b-hero-dash__add"><svg width="20" height="20" viewBox="0 0 20 20" stroke="#fff" stroke-width="2"><path d="M10 2v16M2 10h16"/></svg></div>
    <div class="b-hero-dash__lbl" style="top:163px"><?php echo esc_html($d['side_add']); ?></div>
    <svg style="position:absolute;left:27px;top:212px" width="23" height="23" viewBox="0 0 23 23" fill="none" stroke="#c9ced3" stroke-width="1.6"><circle cx="9.5" cy="9.5" r="7.5"/><path d="m15 15 6.5 6.5"/></svg>
    <div class="b-hero-dash__lbl" style="top:249px;color:#dfe3e6"><?php echo esc_html($d['side_search']); ?></div>
    <div class="b-hero-dash__rule" style="top:281px;opacity:.5"></div>
    <div class="b-hero-dash__nav-sel"></div>
    <svg style="position:absolute;left:29px;top:299px" width="19" height="23" viewBox="0 0 19 23" fill="#fff"><path d="M2 1h15v21H2zM5 4v3h3V4zm6 0v3h3V4zM5 9v3h3V9zm6 0v3h3V9zm-6 5v3h3v-3zm6 0v3h3v-3z" fill-rule="evenodd"/></svg>
    <div class="b-hero-dash__lbl" style="top:335px"><?php echo esc_html($d['side_assets']); ?></div>
    <div class="b-hero-dash__rule" style="top:369px"></div>
    <div class="b-hero-dash__avatar"><?php echo esc_html($d['side_initials']); ?></div>
    <div class="b-hero-dash__lbl" style="top:800px;color:#dfe3e6"><?php echo esc_html($d['side_profile']); ?></div>
  </div>

  <div class="b-hero-dash__main">
    <div class="b-hero-dash__title"><?php echo esc_html($d['title']); ?></div>
    <div class="b-hero-dash__subtitle"><?php echo esc_html($d['subtitle']); ?></div>
    <div class="b-hero-dash__lab" style="left:0"><?php echo esc_html($d['location_label']); ?></div>
    <div class="b-hero-dash__sel" style="left:0;width:288px"><?php echo esc_html($d['location_value']); ?> <i></i></div>
    <div class="b-hero-dash__lab" style="left:302px"><?php echo esc_html($d['range_label']); ?></div>
    <div class="b-hero-dash__sel b-hero-dash__sel--on" style="left:302px;width:215px" data-dash-range><?php echo esc_html($d['range_value']); ?> <i></i></div>
    <?php if ($d['info']) : ?>
      <div class="b-hero-dash__info">ⓘ <?php echo esc_html($d['info']); ?></div>
    <?php endif; ?>
    <div class="b-hero-dash__export"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="#2b2f33" stroke-width="1.2"><path d="M7 1v8M3.5 5.5 7 9l3.5-3.5M1 9.5v3h12v-3"/></svg><?php echo esc_html($d['export_label']); ?></div>

    <?php foreach ($kpis as $i => $kpi) : ?>
      <div class="b-hero-dash__card b-hero-dash__card--kpi" style="left:<?php echo $i * 218; ?>px;animation-delay:<?php echo $card_delay + $i * 0.07; ?>s">
        <div class="b-hero-dash__k-l"><?php echo esc_html($kpi['label']); ?></div>
        <div class="b-hero-dash__k-v" data-count="<?php echo (int) $kpi['value']; ?>">0</div>
        <?php echo $icon($kpi['icon']); ?>
      </div>
    <?php endforeach; ?>
    <div class="b-hero-dash__card b-hero-dash__card--wide" style="animation-delay:.4s">
      <div class="b-hero-dash__k-l"><?php echo esc_html($d['wide_label']); ?></div>
      <div class="b-hero-dash__k-v" data-count="<?php echo (int) $d['wide_value']; ?>">0</div>
      <?php echo $icon($d['wide_icon']); ?>
    </div>

    <div class="b-hero-dash__card b-hero-dash__card--chart" style="left:0;animation-delay:.48s">
      <h4><?php echo esc_html($d['donut_title']); ?></h4><div class="b-hero-dash__hr"></div>
      <svg width="526" height="246" viewBox="0 0 526 246">
        <g transform="translate(263 123) rotate(-90)">
          <?php foreach (array_reverse($arcs) as $arc) : if ($arc['to'] <= $arc['from']) continue; ?>
            <circle class="b-hero-dash__donut-seg" r="89" fill="none" stroke="<?php echo esc_attr($arc['colour']); ?>" stroke-width="36" data-from="<?php echo esc_attr($arc['from']); ?>" data-to="<?php echo esc_attr($arc['to']); ?>"/>
          <?php endforeach; ?>
        </g>
      </svg>
      <div class="b-hero-dash__legend">
        <?php foreach ($segments as $segment) : ?>
          <span><i style="background:<?php echo esc_attr($segment['colour']); ?>"></i><?php echo esc_html($segment['label']); ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="b-hero-dash__card b-hero-dash__card--chart" style="left:545px;animation-delay:.56s">
      <h4><?php echo esc_html($d['line_title']); ?></h4><div class="b-hero-dash__hr"></div>
      <svg width="526" height="270" viewBox="0 0 526 270" data-dash-line data-points="<?php echo esc_attr(wp_json_encode($points)); ?>"></svg>
    </div>

    <div class="b-hero-dash__card b-hero-dash__card--low" style="left:0;animation-delay:.64s"><h4><?php echo esc_html($d['lower_left_title']); ?></h4><div class="b-hero-dash__hr"></div></div>
    <div class="b-hero-dash__card b-hero-dash__card--low" style="left:545px;animation-delay:.7s"><h4><?php echo esc_html($d['lower_right_title']); ?></h4><div class="b-hero-dash__hr"></div></div>
  </div>

  <svg class="b-hero-dash__cursor" viewBox="0 0 22 22" data-dash-cursor><path d="M3 2l15 9-6.5 1.6L9 19z" fill="#2b2f33" stroke="#fff" stroke-width="1.4" stroke-linejoin="round"/></svg>
  <div class="b-hero-dash__tip" data-dash-tip data-unit="<?php echo esc_attr($d['tooltip_unit']); ?>"></div>
</div>
