<?php
namespace Jm\WpHelper\Helper;

class Acf
{
    /**
     * @param $type
     * @return array|null
     */
    public static function get_post_assets($type = null) : ?array {
        if($type !== null) {
            $field = (!is_home()) ? get_field($type) : get_field($type, get_option('page_for_posts'));
            if(null !== $field) {
                return preg_split('/\r\n|\r|\n/', $field);
            }
        }
        return [];
    }
}


