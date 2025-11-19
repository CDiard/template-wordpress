<?php
$twig = $GLOBALS['twig'];

$post_data = [];

if (have_posts()) {
    while (have_posts()) {
        the_post();

        $post_data = [
            'title' => get_the_title(),
            'content' => apply_filters('the_content', get_the_content()),
            'date' => get_the_date(),
            'author' => get_the_author(),
            'categories' => get_the_category(),
            'tags' => get_the_tags(),
            'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
        ];
    }
}

$navigation = [
    'prev' => get_previous_post(),
    'next' => get_next_post(),
];

echo $twig->render('pages/single.twig', [
    'post' => $post_data,
    'navigation' => [
        'prev' => $navigation['prev'] ? [
            'title' => $navigation['prev']->post_title,
            'url' => get_permalink($navigation['prev']->ID),
        ] : null,

        'next' => $navigation['next'] ? [
            'title' => $navigation['next']->post_title,
            'url' => get_permalink($navigation['next']->ID),
        ] : null,
    ]
]);