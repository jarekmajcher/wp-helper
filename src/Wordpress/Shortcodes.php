<?php
namespace Jm\WpHelper\Wordpress;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper;

class Shortcodes extends \Jm\WpHelper\WpHelper {

    public function __construct() {

        parent::__construct();

        add_shortcode('themeimage', [$this, 'themeimage']);
        add_shortcode('image', [$this, 'image']);
        add_shortcode('icon', [$this, 'icon']);
        add_shortcode('options', [$this, 'options']);
        add_shortcode('social', [$this, 'social']);
        add_shortcode('block', [$this, 'block']);
    }

    /**
     * @param $atts
     * @return string
     */
    public function themeimage($atts) : string {
        $config = shortcode_atts([
            'image' => null,
            'class' => '',
            'alt' => ''
        ], $atts);

        $image = sprintf('<img src="%1$s" alt="%2$s" class="%3$s" />' ,
            $this->get_filename('rwd', 'images/' . $config['image']),
            $config['alt'],
            $config['class']
        );

        return $this->prepare_lzay_load($image);
    }

    /**
     * @param $atts
     * @return string
     */
    public function image($atts) : string {
        $config = shortcode_atts([
            'id' => null,
            'size' => 'full',
            'class' => ''
        ], $atts);

        $image = wp_get_attachment_image($config['id'], $config['size'], false, ['class' => $config['class']]);

        return $this->prepare_lzay_load($image);
    }

    /**
     * @param $atts
     * @return string
     */
    public function icon($atts) : string {
        $config = shortcode_atts([
            'icon' => '',
            'class' => ''
        ], $atts);

        if($config['icon'] == '') {
            return '';
        }

        return sprintf('<svg class="%1$s"><use xlink:href="#%2$s"/></svg>', WpHelper\Helper\Html::prepare_class('icon', $config['class']), $config['icon']);
    }

    /**
     * @param $atts
     * @return string
     */
    public function options($atts) : string {
        $config = shortcode_atts([
            'field' => null,
        ], $atts);

        return get_field($config['field'], 'option');
    }

    /**
     * @param $atts
     * @return string
     */
    public function social($atts) : string {
        $config = shortcode_atts([
            'class' => 'social',
            'xfn' => 'noopener noreferrer nofollow'
        ], $atts);

        $social = get_field('social', 'option');

        $html = '';

        $html .= sprintf('<div class="%1$s">', $config['class']);
        foreach($social as $key => $value) {
            if($value) {
                $html .= sprintf('<a href="%1$s" class="%2$s" target="_blank" rel="%5$s"><svg class="%3$s"><use xlink:href="#%4$s"/></svg></a>', $value, $config['class'] . '__link', $config['class'] . '__icon', $key, $config['xfn']);
            }
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @param $atts
     * @return string
     */
    public function block($atts) : string {
        $config = shortcode_atts([
            'id' => null,
        ], $atts);

        if(null === $config['id']) {
            return '';
        }

        $post = get_post($config['id']);

        if($post && $post->post_type === 'wp_block') {
            return do_shortcode(do_blocks($post->post_content));
        }
        else {
            return '';
        }
    }
}
