<?php
// Inline SVG icons used by the header and footer
function zinc_icon($name, $class = '') {
	$icons = array(
		'chevron'	=> '<svg width="9" height="6" viewBox="0 0 9 6" fill="none"><path d="M1 1.25 4.5 4.75 8 1.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'arrow'		=> '<svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 8 8 2M3.25 2H8v4.75" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'search'	=> '<svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.3"/><path d="m10.5 10.5 3.5 3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>',
		'phone'		=> '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M15.75 12.69v2.25a1.5 1.5 0 0 1-1.64 1.5 14.84 14.84 0 0 1-6.47-2.3 14.63 14.63 0 0 1-4.5-4.5 14.84 14.84 0 0 1-2.3-6.5A1.5 1.5 0 0 1 2.33 1.5h2.25a1.5 1.5 0 0 1 1.5 1.29c.1.72.27 1.42.53 2.1a1.5 1.5 0 0 1-.34 1.58l-.95.95a12 12 0 0 0 4.5 4.5l.95-.95a1.5 1.5 0 0 1 1.58-.34c.68.26 1.38.43 2.1.53a1.5 1.5 0 0 1 1.3 1.53Z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'email'		=> '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1.75" y="3.5" width="14.5" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="m2.25 4.5 6.75 5.25 6.75-5.25" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'play'		=> '<svg width="50" height="50" viewBox="0 0 50 50" fill="none"><circle cx="25" cy="25" r="25" fill="#fff"/><path d="M20.5 16.5v17L34 25l-13.5-8.5Z" fill="#272727"/></svg>',
		'menu'		=> '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M3 7h18M3 12h18M3 17h18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'close'		=> '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
	);

	if (!isset($icons[$name])) {
		return '';
	}

	$attrs = ' class="c-icon c-icon--' . esc_attr($name) . ($class ? ' ' . esc_attr($class) : '') . '" aria-hidden="true" focusable="false"';
	return preg_replace('/^<svg/', '<svg' . $attrs, $icons[$name]);
}

// href/target/rel attributes for an ACF link array
function zinc_link_attrs($link) {
	$attrs = ' href="' . esc_url($link['url']) . '"';
	if (!empty($link['target'])) {
		$attrs .= ' target="' . esc_attr($link['target']) . '"';
		if ($link['target'] === '_blank') {
			$attrs .= ' rel="noopener"';
		}
	}
	return $attrs;
}

// Escaped title with a trailing full stop picked out in the accent colour
function zinc_accent_title($text) {
	$text = trim((string) $text);
	if (substr($text, -1) === '.') {
		return esc_html(substr($text, 0, -1)) . '<span class="u-accent">.</span>';
	}
	return esc_html($text);
}

// Escaped heading text: line breaks kept, and each sentence's full stop picked out in the accent colour
function zinc_heading($text) {
	$html = esc_html(trim((string) $text));
	$html = preg_replace('/(?<=[^\s.])\.(?=\s|$)/u', '<span class="u-accent">.</span>', $html);
	return nl2br($html, false);
}

// class (with the editor's Additional CSS class) and anchor id attributes for a block's wrapper
function zinc_block_attrs($block, $class, $is_preview = false) {
	if (!empty($block['className'])) {
		$class .= ' ' . $block['className'];
	}
	if ($is_preview) {
		$class .= ' is-preview';
	}
	$attrs = ' class="' . esc_attr($class) . '"';
	if (!empty($block['anchor'])) {
		$attrs .= ' id="' . esc_attr($block['anchor']) . '"';
	}
	return $attrs;
}

// Reading time for a post, e.g. "8 min read"
function zinc_read_time($post) {
	$words = str_word_count(wp_strip_all_tags(strip_shortcodes(get_post_field('post_content', $post))));
	return sprintf(__('%d min read'), max(1, (int) ceil($words / 200)));
}

// Autoplaying embed URL for a YouTube or Vimeo link (watch, share, embed or Shorts links), or '' if it isn't one
function zinc_video_embed_url($url) {
	$url = trim((string) $url);
	if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([\w-]{11})~', $url, $m)) {
		return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1&rel=0&playsinline=1';
	}
	if (preg_match('~vimeo\.com/(?:video/)?(\d+)(?:/([\da-f]+))?~', $url, $m)) {
		$hash = !empty($m[2]) ? $m[2] : (preg_match('~[?&]h=([\da-f]+)~', $url, $h) ? $h[1] : '');
		return 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1&dnt=1' . ($hash ? '&h=' . $hash : '');
	}
	return '';
}

// <img> tag for an ACF image array
/**
 * A single-colour uploaded SVG icon, inline and filled with currentColor so CSS can colour it (e.g. on
 * hover). Its own fills, opacity and ids are dropped. Anything that isn't an SVG falls back to an <img>.
 */
function zinc_inline_icon($image, $class = '') {
	if (empty($image['url'])) {
		return '';
	}
	$file = !empty($image['ID']) ? get_attached_file($image['ID']) : '';
	$svg  = ($file && preg_match('/\.svg$/i', $file) && is_readable($file)) ? (string) file_get_contents($file) : '';
	if (!preg_match('/<svg\b.*<\/svg>/is', $svg, $m)) {
		return zinc_image($image, $class, '', false);
	}

	$svg = preg_replace('/<(script|style|foreignObject)\b.*?<\/\1>/is', '', $m[0]);
	$svg = preg_replace('/\s(on\w+|id|opacity|fill-opacity|stroke-opacity)="[^"]*"/i', '', $svg);
	$svg = preg_replace('/\sfill="(?!none)[^"]*"/i', ' fill="currentColor"', $svg);
	$svg = preg_replace('/\sstroke="(?!none)[^"]*"/i', ' stroke="currentColor"', $svg);
	$attrs = ' aria-hidden="true" focusable="false"' . ($class ? ' class="' . esc_attr($class) . '"' : '');
	return preg_replace('/<svg\b/i', '<svg' . $attrs, $svg, 1);
}

function zinc_image($image, $class = '', $alt = null, $lazy = true) {
	if (empty($image['url'])) {
		return '';
	}

	$html = '<img src="' . esc_url($image['url']) . '"';
	$html .= ' alt="' . esc_attr($alt !== null ? $alt : $image['alt']) . '"';
	if ($class) {
		$html .= ' class="' . esc_attr($class) . '"';
	}
	if (!empty($image['width']) && !empty($image['height']) && $image['width'] > 1) {
		$html .= ' width="' . (int) $image['width'] . '" height="' . (int) $image['height'] . '"';
	}
	return $html . ($lazy ? ' loading="lazy"' : '') . ' decoding="async" />';
}

// Languages configured in the GTranslate plugin (Settings → GTranslate), for the header language switcher
function zinc_gtranslate_languages() {
	if (!class_exists('GTranslate') || !shortcode_exists('gt-link')) {
		return array();
	}

	$data = get_option('GTranslate');
	GTranslate::load_defaults($data);
	$settings = GTranslate::load_settings($data);

	if (empty($settings['languages'])) {
		return array();
	}

	return array(
		'default'   => $settings['default_language'],
		'languages' => array_values($settings['languages']),
	);
}
