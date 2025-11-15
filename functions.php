<?php
/**
 * Template WordPress functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Template_WordPress
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function template_wordpress_setup() {
	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary-menu' => esc_html__( 'Menu principal', 'template-wordpress' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'template_wordpress_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'template_wordpress_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function template_wordpress_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Barre latérale', 'template-wordpress' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Ajoutez des widgets ici.', 'template-wordpress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'template_wordpress_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function template_wordpress_scripts() {
    $theme_version = wp_get_theme()->get('Version');

    // Load Bootstrap CSS from a CDN
    wp_enqueue_style(
        'bootstrap-css',
        get_template_directory_uri() . '/node_modules/bootstrap/dist/css/bootstrap.min.css',
        [],
        '5.3.8'
    );

    // Load the main theme CSS (style.css required by WordPress)
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        ['bootstrap-css'],
        $theme_version
    );

    // Upload your custom CSS (in /css/custom.css)
    wp_enqueue_style(
        'custom-css',
        get_template_directory_uri() . '/css/import.css',
        ['theme-style'],
        $theme_version
    );

    // Load Bootstrap JS (bundle with Popper included)
    wp_enqueue_script(
        'bootstrap-js',
        get_template_directory_uri() . '/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
        [],
        '5.3.8',
        true
    );

    // Upload your custom script
    wp_enqueue_script(
        'theme-js',
        get_template_directory_uri() . '/js/main.js',
        ['bootstrap-js'],
        $theme_version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'template_wordpress_scripts' );

/**
 * Disable/hide items in the administration menu
 */
function template_wordpress_remove_menus(): void
{
    remove_menu_page( 'edit-comments.php' );

    // Hidden from everyone except admin
    if (!current_user_can('administrator')) {
        remove_menu_page('themes.php');
        remove_menu_page('plugins.php');
        remove_menu_page('users.php');
        remove_menu_page('tools.php');
        remove_menu_page('options-general.php');
    }
};
add_action( 'admin_menu', 'template_wordpress_remove_menus' );

/**
 * TWIG initialization.
 */
add_action('after_setup_theme', function() {
    require get_template_directory() . '/inc/twig-init.php';
});

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
