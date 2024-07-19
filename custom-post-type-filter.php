<?php
/*
Plugin Name: Custom Post Type Filter
Description: A plugin to filter custom post type posts.
Version: 1.0
Author: Rahul kumar
*/

if (!defined('ABSPATH')) {
    exit;
}

function rf_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('rf-ajax-filter', plugins_url('/ajax-filter.js', __FILE__), array('jquery'), null, true);

    wp_localize_script('rf-ajax-filter', 'ajaxfilter', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));

    wp_enqueue_style('rf-styles', plugins_url('/styles.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'rf_enqueue_scripts');


function rf_filter_resources_by_taxonomy() {
    $resource_type = isset($_GET['resource_type']) ? intval($_GET['resource_type']) : '';
    $resource_topic = isset($_GET['resource_topic']) ? intval($_GET['resource_topic']) : '';
    $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';

    $args = array(
        'post_type' => 'resources',
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'AND'),
        's' => $keyword,
    );

    if ($resource_type) {
        $args['tax_query'][] = array(
            'taxonomy' => 'resource_type',
            'field'    => 'term_id',
            'terms'    => $resource_type,
        );
    }

    if ($resource_topic) {
        $args['tax_query'][] = array(
            'taxonomy' => 'resource_topic',
            'field'    => 'term_id',
            'terms'    => $resource_topic,
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('grid-item'); ?>>
                <a href="<?php the_permalink(); ?>">
                    <?php
                    if ( has_post_thumbnail() ) {
                        echo '<div class="post-thumbnail">' . get_the_post_thumbnail() . '</div>';
                    }
                    ?>
                    <h2><?php the_title(); ?></h2>
                    <div class="post-excerpt"><?php the_excerpt(); ?></div>
                </a>
            </article>
            <?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>No resources found.</p>';
    endif;

    wp_die();
}
add_action('wp_ajax_rf_filter_resources', 'rf_filter_resources_by_taxonomy');
add_action('wp_ajax_nopriv_rf_filter_resources', 'rf_filter_resources_by_taxonomy');


function rf_filter_form_shortcode() {
    ob_start(); ?>

    <div id="filter-section">
        <form id="filter-form" method="GET">
            
            <select name="resource_type" id="resource_type">
                <option value="">Select Resource Type</option>
                <?php 
                $resource_types = get_terms(array(
                    'taxonomy' => 'resource_type',
                    'hide_empty' => false,
                ));
                foreach ($resource_types as $type) {
                    echo '<option value="' . esc_attr($type->term_id) . '"' . (isset($_GET['resource_type']) && $_GET['resource_type'] == $type->term_id ? ' selected' : '') . '>' . esc_html($type->name) . '</option>';
                }
                ?>
            </select>

            <select name="resource_topic" id="resource_topic">
                <option value="">Select Resource Topic</option>
                <?php 
                $resource_topics = get_terms(array(
                    'taxonomy' => 'resource_topic',
                    'hide_empty' => false,
                ));
                foreach ($resource_topics as $topic) {
                    echo '<option value="' . esc_attr($topic->term_id) . '"' . (isset($_GET['resource_topic']) && $_GET['resource_topic'] == $topic->term_id ? ' selected' : '') . '>' . esc_html($topic->name) . '</option>';
                }
                ?>
            </select>

            <input type="text" name="keyword" id="keyword" placeholder="Search by keyword" value="<?php echo isset($_GET['keyword']) ? esc_attr($_GET['keyword']) : ''; ?>">

            <button type="submit" id="filter-button">Filter</button>
        </form>

        <!-- Loader -->
        <div id="loader" style="display:none;">
            <div class="loader-spinner"></div>
        </div>
    </div>

    <?php return ob_get_clean();
}
add_shortcode('rf_filter_form', 'rf_filter_form_shortcode');
