<?php

if (!defined('ABSPATH')) exit;

function get_bootstrap_pagination_html($svg_icon_callback): string
{
    global $wp_query;

    if ($wp_query->max_num_pages <= 1) return '';

    $current = max(1, get_query_var('paged'));
    $total = $wp_query->max_num_pages;
    $max_links = 3;

    $half = floor($max_links / 2);

    $start = $current - $half;
    $end = $current + $half;

    if ($start < 1) {
        $start = 1;
        $end = min($max_links, $total);
    }

    if ($end > $total) {
        $end = $total;
        $start = max(1, $total - $max_links + 1);
    }

    $arrow_next = $svg_icon_callback('arrow_next');
    $arrow_previous = $svg_icon_callback('arrow_previous');
    $arrow_first = $svg_icon_callback('arrow_first');
    $arrow_last = $svg_icon_callback('arrow_last');

    $html = '<nav class="pagination-wrapper" role="navigation" aria-label="Pagination"><ul class="pagination">';

    // FIRST
    if ($current > 1) {
        $html .= '<li class="page-item">
                    <a class="page-link" href="' . esc_url(get_pagenum_link(1)) . '" title="Première page" aria-label="Première page">' . $arrow_first . '</a>
                  </li>';
    }

    // PREV
    if ($current > 1) {
        $html .= '<li class="page-item">
                    <a class="page-link" href="' . esc_url(get_pagenum_link($current - 1)) . '" title="Page précédente" aria-label="Page précédente">' . $arrow_previous . '</a>
                  </li>';
    }

    // PAGE NUMBERS (LIMITED TO 3)
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $current ? ' active' : '';

        $html .= '<li class="page-item' . $active . '">
                    <a class="page-link" href="' . esc_url(get_pagenum_link($i)) . '" title="Page ' . $i . '" aria-label="Page ' . $i . '">' . $i . '</a>
                  </li>';
    }

    // NEXT
    if ($current < $total) {
        $html .= '<li class="page-item">
                    <a class="page-link" href="' . esc_url(get_pagenum_link($current + 1)) . '" title="Page suivante" aria-label="Page suivante">' . $arrow_next . '</a>
                  </li>';
    }

    // LAST
    if ($current < $total) {
        $html .= '<li class="page-item">
                    <a class="page-link" href="' . esc_url(get_pagenum_link($total)) . '" title="Dernière page" aria-label="Dernière page">' . $arrow_last . '</a>
                  </li>';
    }

    $html .= '</ul></nav>';

    return $html;
}