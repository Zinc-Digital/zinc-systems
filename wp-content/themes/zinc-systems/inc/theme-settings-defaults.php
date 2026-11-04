<?php
// One-off: pre-fill the Header & Footer Theme Settings with the copy from the design.
// Runs the first time an admin loads wp-admin, and only if the settings are still empty.
function zinc_seed_header_footer_settings() {
	if (!function_exists('update_field') || get_option('zinc_header_footer_seeded') || !current_user_can('manage_options')) {
		return;
	}

	update_option('zinc_header_footer_seeded', 1);

	if (get_field('header_nav', 'option') || get_field('footer_columns', 'option')) {
		return;
	}

	$link = function ($title, $url = '#') {
		return array('title' => $title, 'url' => $url, 'target' => '');
	};
	$links = function ($titles) use ($link) {
		return array_map(function ($title) use ($link) {
			return array('link' => $link($title));
		}, $titles);
	};
	$items = function ($rows) use ($link) {
		return array_map(function ($row) use ($link) {
			return array('icon' => '', 'title' => $row[0], 'description' => isset($row[1]) ? $row[1] : '', 'link' => $link($row[0]));
		}, $rows);
	};

	$platform_menu = array(
		'columns' => array(
			array(
				'heading'     => 'By capability',
				'footer_link' => $link('View platform'),
				'items'       => $items(array(
					array('Incident Management', 'Log it, escalate it, task it, close it. One audit trail.'),
					array('Case Management', 'Link incidents into an investigation with evidence and outcomes.'),
					array('Threat Intelligence', 'Risk profiling and live monitoring around every site.'),
					array('Mass Notifications', 'Alerts across your sites in seconds.'),
					array('Compliance', 'Proof of presence. Scored inspections. No gaps.'),
					array('Daily Operations', 'The front desk, the post room and the key cabinet, ran properly.'),
				)),
			),
			array(
				'heading'     => 'By use case',
				'footer_link' => $link('View all use cases'),
				'items'       => $items(array(
					array('Martyn’s Law Compliance'),
					array('Crisis Management'),
					array('Control Room'),
					array('Global Security Operations Centre'),
					array('Retail Loss Prevention'),
					array('Health and Safety'),
					array('Executive Protection'),
				)),
			),
		),
		'show_feature' => 1,
		'feature'      => array(
			'image'     => '',
			'caption'   => 'Platform tour · 3 min',
			'show_play' => 1,
			'title'     => 'See all fourteen modules working as one.',
			'text'      => 'One incident, from log to board report, in a matter of minutes.',
			'link'      => $link('Watch the tour'),
		),
		'show_bottom_bar' => 1,
		'bottom_bar'      => array(
			'label' => 'Works alongside every module',
			'links' => $links(array('Reporting and AI', 'Integrations', 'Admin Panel')),
		),
		'background_image' => '',
	);

	update_field('field_zs_hdr_nav', array(
		array('label' => 'Platform', 'link' => '', 'has_mega_menu' => 1, 'mega_menu' => $platform_menu),
		array('label' => 'Why Zinc', 'link' => $link('Why Zinc'), 'has_mega_menu' => 0),
		array('label' => 'Insights', 'link' => $link('Insights'), 'has_mega_menu' => 0),
	), 'option');
	update_field('field_zs_hdr_show_search', 1, 'option');
	update_field('field_zs_hdr_search_placeholder', 'Search', 'option');
	update_field('field_zs_hdr_button', $link('Book a demo'), 'option');
	update_field('field_zs_hdr_region_label', 'UK', 'option');

	update_field('field_zs_ftr_cta_enabled', 1, 'option');
	update_field('field_zs_ftr_cta', array(
		'title'            => 'Ready to command operational security?',
		'text'             => 'Schedule a detailed, tailored platform run-through with an expert from our enterprise security engineering team today.',
		'primary_button'   => $link('Book a tailored demo'),
		'secondary_button' => $link('Discover use cases'),
	), 'option');
	update_field('field_zs_ftr_phone', '+44 20 3989 4859', 'option');
	update_field('field_zs_ftr_email', 'info@zinc.systems', 'option');
	update_field('field_zs_ftr_office_label', 'UK Office:', 'option');
	update_field('field_zs_ftr_address', "The Old Garage\nBedford Road\nLittle Houghton\nNorthampton\nNN7 1AB", 'option');
	update_field('field_zs_ftr_columns', array(
		array('groups' => array(
			array('heading' => 'Sectors', 'heading_link' => '', 'links' => $links(array('Commercial Real Estate', 'Security Providers', 'Retail', 'Enterprise'))),
		)),
		array('groups' => array(
			array('heading' => 'Company', 'heading_link' => '', 'links' => $links(array('About Us', 'Careers'))),
			array('heading' => 'Customer Support', 'heading_link' => $link('Customer Support'), 'links' => array()),
		)),
	), 'option');
	update_field('field_zs_ftr_button', $link('Get in touch'), 'option');
	update_field('field_zs_ftr_legal_links', $links(array('Privacy Policy', 'Cookie Policy', 'Carbon Reduction Policy')), 'option');
	update_field('field_zs_ftr_copyright', "Copyright © {year} Zinc Systems.\nA trading name of Zinc Digital Business Solutions Ltd.\nRegistered in England & Wales No. 05235293", 'option');
	update_field('field_zs_ftr_tagline', 'See more. Risk less.', 'option');
}
add_action('admin_init', 'zinc_seed_header_footer_settings');
