<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/../vendor/autoload.php';

require_once get_template_directory() . '/configs/pagination.php';

use ElementorDeps\Twig\Environment;
use ElementorDeps\Twig\Loader\FilesystemLoader;
use ElementorDeps\Twig\TwigFunction;


/**
 * ------------------------------------------------------------
 * TWIG INIT
 * ------------------------------------------------------------
 */

// Paths
$views_dir = get_template_directory() . '/views';
$cache_dir = get_template_directory() . '/cache/twig';

// Loader + environment
$loader = new FilesystemLoader($views_dir);
$twig = new Environment($loader, [
    'cache' => (defined('WP_DEBUG') && WP_DEBUG) ? false : $cache_dir,
    'debug' => defined('WP_DEBUG') ? WP_DEBUG : false,
]);


/**
 * ------------------------------------------------------------
 * UTIL : Capture function output (for echo-based WP functions)
 * ------------------------------------------------------------
 */

$capture = function ($fn) {
    return function (...$args) use ($fn) {
        ob_start();
        call_user_func_array($fn, $args);
        return ob_get_clean();
    };
};


/**
 * ------------------------------------------------------------
 * AUTO REGISTER SIMPLE WORDPRESS ECHO FUNCTIONS
 * ------------------------------------------------------------
 */

$echo_functions = [
    'wp_head',
    'wp_footer',
    'wp_body_open',
];

foreach ($echo_functions as $func) {
    if (function_exists($func)) {
        $twig->addFunction(new TwigFunction($func, $capture($func), ['is_safe' => ['html']]));
    }
}


/**
 * ------------------------------------------------------------
 * BODY CLASS, LANGUAGE ATTRIBUTES, SIDEBARS, MENUS
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('body_class', function () {
    return implode(' ', get_body_class());
}, ['is_safe' => ['html']]));

$twig->addFunction(new TwigFunction('language_attributes', $capture('language_attributes'), ['is_safe' => ['html']]));

$twig->addFunction(new TwigFunction('dynamic_sidebar', $capture('dynamic_sidebar'), ['is_safe' => ['html']]));

$twig->addFunction(new TwigFunction('wp_nav_menu', function ($args = []) {
    $args = wp_parse_args($args, ['echo' => false]);
    return wp_nav_menu($args);
}, ['is_safe' => ['html']]));

$twig->addFunction(new TwigFunction('flat_menu', function ($location, $classes = '') {

    return wp_nav_menu([
        'theme_location' => $location,
        'container' => false,
        'echo' => false,
        'walker' => new Walker_Nav_Flat(),
        'items_wrap' => '<nav class="' . esc_attr($classes) . '" role="navigation" aria-label="Menu principal">%3$s</nav>',
    ]);

}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * "GETTERS" (return HTML-safe strings)
 * ------------------------------------------------------------
 */

$return_functions = [
    'bloginfo' => 'get_bloginfo',
    'home_url' => 'home_url',
    'get_the_title' => 'get_the_title',
    'get_the_excerpt' => 'get_the_excerpt',
    'get_the_permalink' => 'get_permalink',
    'get_search_query' => 'get_search_query',
];

foreach ($return_functions as $twig_name => $wp_function) {
    $twig->addFunction(new TwigFunction($twig_name, function (...$args) use ($wp_function) {
        return call_user_func_array($wp_function, $args);
    }, ['is_safe' => ['html']]));
}

$twig->addFunction(new TwigFunction('the_custom_logo', function () {
    return get_custom_logo();
}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * CONSTANT
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('constant', function ($name) {
    return defined($name) ? constant($name) : null;
}));


/**
 * ------------------------------------------------------------
 * TRANSLATION
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('__', function ($text, $domain = null) {
    return $domain ? __($text, $domain) : __($text);
}));


/**
 * ------------------------------------------------------------
 * SEARCH FORM
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('search_form', function () use ($twig) {
    return $twig->render('components/search.twig');
}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * BOOLEAN HELPERS
 * ------------------------------------------------------------
 */

$boolean_functions = [
    'has_nav_menu',
    'is_front_page',
    'is_home',
    'is_customize_preview',
];

foreach ($boolean_functions as $func) {
    if (function_exists($func)) {
        $twig->addFunction(new TwigFunction($func, $func));
    }
}


/**
 * ------------------------------------------------------------
 * IMPORT ICONS
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('svg_icon', function ($name) {
    $path = get_template_directory() . "/assets/icons/{$name}.svg";

    if (!file_exists($path)) {
        return "<!-- SVG '{$name}' not found -->";
    }

    $svg = file_get_contents($path);

    if (preg_match('/class="([^"]*)"/', $svg, $matches)) {
        $existing = $matches[1] . ' custom-icon';
        $svg = preg_replace('/class="[^"]*"/', 'class="' . esc_attr($existing) . '"', $svg, 1);
    } else {
        $svg = preg_replace('/<svg([^>]*)>/', '<svg$1 class="custom-icon">', $svg, 1);
    }

    return $svg;
}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * PAGE CONTEXT → to avoid writing PHP code in page.php
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('get_page_context', function ($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $post = get_post($post_id);

    if (!$post) return [];

    return [
        'id' => $post->ID,
        'title' => get_the_title($post),
        'content' => apply_filters('the_content', $post->post_content),
        'thumbnail' => get_the_post_thumbnail_url($post, 'large'),
        'permalink' => get_permalink($post),
        'classes' => implode(' ', get_body_class()),
    ];
}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * GET ALL POSTS
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('get_posts_context', function ($args = []) {
    $defaults = [
        'post_type' => 'post',
        'posts_per_page' => -1,
        'paged' => 1,
    ];
    $args = wp_parse_args($args, $defaults);

    $query = new WP_Query($args);
    $posts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $thumbnail_id = get_post_thumbnail_id(get_the_ID());
            $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            $posts[] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'content' => apply_filters('the_content', get_the_content()),
                'permalink' => get_permalink(),
                'date' => get_the_date(),
                'author' => get_the_author(),
                'thumbnail' => $thumbnail_url,
                'thumbnail_alt' => $thumbnail_alt ?: get_the_title(),
                'post_type' => get_post_type(),
            ];
        }
        wp_reset_postdata();
    }

    return $posts;
}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * PAGINATION
 * ------------------------------------------------------------
 */

$twig->addFunction(new TwigFunction('bootstrap_pagination', function () use ($twig) {
    $svg_icon_callback = $twig->getFunction('svg_icon')->getCallable();

    return get_bootstrap_pagination_html($svg_icon_callback);
}, ['is_safe' => ['html']]));


/**
 * ------------------------------------------------------------
 * EXPORT TWIG
 * ------------------------------------------------------------
 */

$GLOBALS['twig'] = $twig;
