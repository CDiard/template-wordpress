<?php

class Walker_Nav_Flat extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ): void
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class=\"submenu depth-$depth\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = null ): void
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</div>\n";
    }

    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ): void
    {
        $classes = !empty($data_object->classes) ? (array) $data_object->classes : [];

        $is_active =
            in_array('current-menu-item', $classes, true) ||
            in_array('current_page_item', $classes, true) ||
            in_array('current-menu-parent', $classes, true) ||
            in_array('current_page_parent', $classes, true) ||
            in_array('current-menu-ancestor', $classes, true);

        if ($is_active) {
            $classes[] = 'active';
        }

        if (in_array('menu-item-has-children', $classes, true)) {
            $classes[] = 'has-children';
        }

        $class_names = implode(' ', array_map('sanitize_html_class', $classes));

        $title = apply_filters('the_title', $data_object->title, $data_object->ID);

        $indent = str_repeat("\t", $depth);

        $output .= $indent . '<a href="' . esc_url($data_object->url) . '" class="nav-item nav-link link-body-emphasis ' . esc_attr($class_names) . '">';
        $output .= esc_html($title);
        $output .= '</a>' . "\n";
    }

    public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
        // No </li>
    }
}
