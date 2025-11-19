<?php
$twig = $GLOBALS['twig'];

$results = [];
$query = get_search_query();

if (have_posts()) {
    while (have_posts()) {
        the_post();

        $results[] = [
            'id'        => get_the_ID(),
            'title'     => get_the_title(),
            'excerpt'   => get_the_excerpt(),
            'content'   => get_the_content(),
            'link'      => get_permalink(),
            'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
        ];
    }
}

echo $twig->render('pages/search.twig', [
    'query'   => $query,
    'results' => $results,
    'has_results' => count($results) > 0,
]);