<?php
namespace Jm\WpHelper\Helper;

/**
 */
class Wordpress
{
    /**
     * @param $content
     * @return string
     */
    public static function wp_replace($content) : string {
        $post = get_post();

        if($post) {
            preg_match_all('/(?<!\d)%([^%]*)\%/', $content, $matches);

            if(array_key_exists(1, $matches)) {
                foreach($matches[1] as $match) {
                    if($post->$match) {
                        $content = strip_tags($content, '<b><i><u><strong><span>');
                        $content = str_replace('%' . $match . '%', $post->$match, $content);
                    }
                }
            }
            return $content;
        }
        else {
            return $content;
        }
    }


    /**
     * @return bool
     */
    public static function is_login_page() : bool {
        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) || in_array($_SERVER['REQUEST_URI'], array('/admin'));
    }

    /**
     * @param $title
     * @param $url
     * @param $order
     * @param $classes
     * @param $parent
     * @return \stdClass
     */
    public static function custom_nav_menu_item($title, $url, $order, $classes = [], $parent = 0) : \stdClass {
        $item = new \stdClass();
        $item->ID = 1000000 + $order + $parent;
        $item->db_id = $item->ID;
        $item->title = $title;
        $item->url = $url;
        $item->menu_order = $order;
        $item->menu_item_parent = $parent;
        $item->type = '';
        $item->object = '';
        $item->object_id = '';
        $item->classes = $classes;
        $item->target = '';
        $item->attr_title = '';
        $item->description = '';
        $item->xfn = '';
        $item->status = '';

        return $item;
    }

    /**
     * @return bool
     */
    public static function is_autosave() : bool {
        return ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']));
    }

    /**
     * @param $position
     * @return void
     */
    public static function add_admin_menu_separator($position) : void {
        global $menu;
        $index = 0;
        if(is_array($menu)) {
            foreach($menu as $offset => $section) {
                if (substr($section[2],0,9) == 'separator')
                    $index++;
                if ($offset>=$position) {
                    $menu[$position] = ['', 'read', "separator{$index}", '', 'wp-menu-separator'];
                    break;
                }
            }
            ksort($menu);
        }
    }

    /**
     * @param $taxonomy
     * @param $postType
     * @param $parent
     * @param $d
     * @return mixed
     */
    public static function get_taxonomy_hierarchy($taxonomy, $postType, $parent = 0, $d = 0) : mixed {
        $taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;

        $terms = \Timber::get_terms($taxonomy, [
            'parent' => $parent,
            'meta_key' => 'order',
            'orderby' => 'meta_value',
            'order' => 'ASC',
        ]);

        $children = [];

        foreach($terms as $term){
            $term->depth = $d;
            $term->terms = self::get_taxonomy_hierarchy($taxonomy, $postType, $term->term_id, $d + 1);
            $term->posts = \Timber::get_posts([
                'posts_per_page' => -1,
                'post_type' => $postType,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'tax_query' => [
                    [
                        'taxonomy' => $taxonomy,
                        'field' => 'id',
                        'terms' => $term->term_id,
                        'include_children' => false
                    ]
                ]
            ]);

            $children[$term->term_id] = $term;
        }

        return $children;
    }
}
