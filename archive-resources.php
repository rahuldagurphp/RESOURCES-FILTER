<?php
get_header(); ?>

<div id="primary" class="content-area">
    <div class="container">
        <!-- Filter Form -->
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

                <button type="submit">Filter</button>
            </form>
             <!-- Loader -->
            <div id="loader" style="display:none;">
              <div class="loader-spinner"></div>
            </div>
        </div>

        
        <div class="posts-sidebar-container">

            <div class="posts-grid">
                <main id="main" class="site-main" role="main">

                    <div id="posts-container">
                        <?php
                    
                        if ( have_posts() ) :
                            while ( have_posts() ) : the_post();
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

                        
                            the_posts_pagination();

                        else :
                            echo '<p>No resources found.</p>';
                        endif;
                        ?>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
