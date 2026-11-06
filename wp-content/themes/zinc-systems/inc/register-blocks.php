<?php 
//Theme Options
if(function_exists('acf_add_options_page')) {
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Header Settings',
		'menu_title'	=> 'Header',
		'parent_slug'	=> 'theme-general-settings',
		'menu_slug' 	=> 'theme-header-settings',
		'capability'	=> 'edit_posts'
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'theme-general-settings',
		'menu_slug' 	=> 'theme-footer-settings',
		'capability'	=> 'edit_posts'
	));
}

//Register blocks
function register_acf_block_types() {

	// Hero (pinned, scroll-animated)
	acf_register_block_type(array(
		'name'				=> 'hero',
		'title'				=> __('Hero'),
		'description'		=> __('Homepage hero with capability chips, product UI and scroll animation'),
		'render_template'	=> 'template-parts/blocks/hero.php',
		'category'			=> 'formatting',
		'mode'				=> 'preview',
		'supports'			=> array('mode' => true, 'anchor' => true, 'align' => false, 'multiple' => false),
		'icon'				=> 'cover-image',
		'enqueue_assets'	=> 'zinc_hero_block_assets'
	));

	// Content blocks: name => title, description, icon, script(s) (in assets/js/blocks/)
	$blocks = array(
		'feature-accordion'	=> array(__('Feature accordion'), __('Image beside a heading and an accordion of features'), 'list-view', 'feature-accordion'),
		'feature-showcase'	=> array(__('Feature showcase'), __('Centred intro above stacking image cards with buttons and a chart overlay'), 'format-image', array('stack', 'reveal')),
		'stats'				=> array(__('Stats'), __('Teal band with a heading and large counting numbers'), 'chart-bar', 'reveal'),
		'case-studies'		=> array(__('Case studies'), __('Quote, results and image, with arrows between slides'), 'format-quote', array('carousel', 'reveal')),
		'insights'			=> array(__('Insights'), __('Scrolling cards for the latest or chosen posts'), 'admin-post', 'carousel'),
		'link-cards'		=> array(__('Link cards'), __('Heading above a row of linked cards'), 'grid-view', ''),
	);

	foreach ($blocks as $name => $block) {
		acf_register_block_type(array(
			'name'				=> $name,
			'title'				=> $block[0],
			'description'		=> $block[1],
			'render_template'	=> 'template-parts/blocks/' . $name . '.php',
			'category'			=> 'formatting',
			'mode'				=> 'preview',
			'supports'			=> array('mode' => true, 'anchor' => true, 'align' => false),
			'icon'				=> $block[2],
			'enqueue_assets'	=> function () use ($block) {
				zinc_block_assets($block[3]);
			}
		));
	}

}

// Content block assets: its scripts (shared between blocks), and the block styles in the editor
function zinc_block_assets($scripts) {
	foreach (array_filter((array) $scripts) as $script) {
		wp_enqueue_script('zinc-block-' . $script, get_template_directory_uri() . '/assets/js/blocks/' . $script . '.js', array(), zinc_asset_version('assets/js/blocks/' . $script . '.js'), true);
	}
	if (is_admin()) {
		wp_enqueue_style('blocks-editor', get_template_directory_uri() . '/assets/css/blocks-editor.css', array(), zinc_asset_version('assets/css/blocks-editor.css'));
	}
}

// Hero block dashboard content: the block's fields, with the example data filling anything not set
function zinc_hero_dashboard_data($fields) {
	$defaults = array(
		'title'             => 'Asset Dashboard',
		'subtitle'          => 'Overview of asset inventory, checkouts, and utilisation metrics',
		'location_label'    => 'Select Location',
		'location_value'    => 'Select Location',
		'range_label'       => 'Date Range',
		'range_value'       => 'Last 12 months',
		'info'              => 'When location is not selected — data is shown across all your subscribed locations.',
		'export_label'      => 'Export PDF',
		'kpis'              => array(
			array('label' => 'Total Active Assets', 'value' => 19, 'icon' => 'assets'),
			array('label' => 'Currently Checked Out', 'value' => 6, 'icon' => 'checked_out'),
			array('label' => 'Overdue Returns', 'value' => 3, 'icon' => 'overdue'),
			array('label' => 'Available (Unassigned)', 'value' => 13, 'icon' => 'available'),
			array('label' => 'Returns Today', 'value' => 0, 'icon' => 'returns'),
		),
		'wide_label'        => 'Deactivated Assets',
		'wide_value'        => 0,
		'wide_icon'         => 'deactivated',
		'donut_title'       => 'Asset Status Breakdown',
		'segments'          => array(
			array('label' => 'Assigned', 'value' => 13.3, 'colour' => '#4CA5DA'),
			array('label' => 'Unassigned', 'value' => 30.3, 'colour' => '#5BC3E3'),
			array('label' => 'Overdue', 'value' => 8.3, 'colour' => '#56CFD2'),
			array('label' => 'Active', 'value' => 48.1, 'colour' => '#6CE0BE'),
			array('label' => 'Deactive', 'value' => 0, 'colour' => '#8FE9CF'),
		),
		'line_title'        => 'Checkouts Over Time',
		'tooltip_unit'      => 'checkouts',
		'points'            => array(
			array('label' => '08 Feb', 'value' => 2),
			array('label' => '09 Feb', 'value' => 3),
			array('label' => '04 Mar', 'value' => 1),
			array('label' => '07 Mar', 'value' => 1),
			array('label' => '24 May', 'value' => 1),
			array('label' => '16 Jun', 'value' => 1),
			array('label' => '29 Jun', 'value' => 1),
		),
		'lower_left_title'  => 'Overdue by Location',
		'lower_right_title' => 'Assets by Type / Category',
		'side_add'          => 'Add',
		'side_search'       => 'Search',
		'side_assets'       => 'Assets',
		'side_profile'      => 'My Profile',
		'side_initials'     => 'MN',
	);

	$fields = is_array($fields) ? $fields : array();
	foreach ($defaults as $key => $default) {
		if (!isset($fields[$key]) || (is_array($default) && empty($fields[$key]))) {
			$fields[$key] = $default;
		}
	}
	return $fields;
}

// Hero block scripts (and its styles in the editor, where main.css isn't loaded)
function zinc_hero_block_assets() {
	wp_enqueue_style('roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null);
	wp_enqueue_script('hero-block', get_template_directory_uri() . '/assets/js/blocks/hero.js', array(), zinc_asset_version('assets/js/blocks/hero.js'), true);

	if (is_admin()) {
		wp_enqueue_style('blocks-editor', get_template_directory_uri() . '/assets/css/blocks-editor.css', array(), zinc_asset_version('assets/css/blocks-editor.css'));
	}
}

if( function_exists('acf_register_block_type') ) {
	add_action('acf/init', 'register_acf_block_types');
}


add_filter( 'acf/the_field/escape_html_optin', '__return_true' );
add_action( 'acf/init', 'set_acf_settings' );
function set_acf_settings() {
    acf_update_setting( 'enable_shortcode', false );
}
?>