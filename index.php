<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Template_WordPress
 */

$twig = $GLOBALS['twig'];

$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$posts = $twig->getFunction('get_posts_context')->getCallable()([
    'post_type' => 'post',
    'posts_per_page' => get_option('posts_per_page'),
    'paged' => $paged,
]);

$pagination = [
    'prev_link' => get_previous_posts_link('« Articles précédents'),
    'next_link' => get_next_posts_link('Articles suivants »'),
];

echo $twig->render('index.twig', [
    'is_home' => is_home(),
    'is_front_page' => is_front_page(),
    'page_title' => single_post_title('', false),
    'posts' => $posts,
    'pagination' => $pagination,
]);