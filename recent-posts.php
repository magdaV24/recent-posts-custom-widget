<?php

/**
 * Plugin Name: Recent Posts
 * Description: A custom plugin that displays the most recent posts;
 * Author: Magda Vasilache
 * Author URI: -
 * Version: 1.0.0
 * Text Domain: recent-posts-custom-plugin
 */

if (!defined('ABSPATH')) {
    exit;
}
function recent_posts_scripts()
{
    wp_enqueue_script('recent-posts-script', plugin_dir_url(__FILE__) . '/js/recent-posts.js', array('jquery'), '1.0', true);
    wp_enqueue_style('recent-posts-style', plugin_dir_url(__FILE__) . '/css/recent-posts.css');
}
add_action('wp_enqueue_scripts', 'recent_posts_scripts');

function register_recent_posts_widget()
{
    register_widget('Custom_Recent_Posts_Widget');
}
add_action('widgets_init', 'register_recent_posts_widget');


class Custom_Recent_Posts_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'recent_posts',
            'Custom Recent Posts Widget',
            array('description' => 'Displays recent posts with avatars.')
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
?>
        <div class="post-widget-title">
            <span class="widget-title-before">
                <?php echo $args['before_title']; ?>
            </span>
            <span class="widget-title-text">
                The latest posts
            </span>
            <span class="widget-title-after">
                <?php echo $args['after_title']; ?>
            </span>
        </div>
        <?php
        $posts_args = array(
            'post_type' => 'post',
            'posts_per_page' => $instance['count'],
        );
        $posts = get_posts($posts_args);

        if ($posts) :
            foreach ($posts as $post) :
                setup_postdata($post);
        ?>
                <div class="post-widget-wrapper">
                    <div class="post-widget-thumbnail-container">
                        <img src="<?php the_post_thumbnail_url() ?>" alt="">
                    </div>
                    <div>
                        <div><a href="<?php the_permalink(); ?>"><?php echo wp_trim_words(get_the_title(), 7, '...'); ?></a></div>
                        <div><?php echo wp_trim_words(get_the_content(), 12, '...'); ?></div>
                    </div>
                </div>
        <?php
            endforeach;
            wp_reset_postdata();
        else :
            echo 'No posts found.';
        endif;

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $count = !empty($instance['count']) ? $instance['count'] : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>">Number of posts to display:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
        </p>
<?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['count'] = (!empty($new_instance['count'])) ? strip_tags($new_instance['count']) : '';
        return $instance;
    }
}
