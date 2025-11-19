<?php
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

echo $twig->render('pages/list.twig', [
    'is_home' => is_home(),
    'is_front_page' => is_front_page(),
    'page_title' => single_post_title('', false),
    'posts' => $posts,
    'pagination' => $pagination,
]);