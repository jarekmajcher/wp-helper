<?php
namespace Jm\WpHelper\Timber;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper;


class Functions extends \Jm\WpHelper\WpHelper {

    public function __construct() {

        parent::__construct();

        add_filter('timber/twig', [$this, 'add_functions_to_twig']);
    }

    function add_functions_to_twig(\Twig\Environment $twig): \Twig\Environment {

        $new_functions = [
            'get_timber_post',
            'get_timber_term',
            'wp_get_attachment_image',
            'get_the_post_thumbnail',
            'get_the_password_form',
            'asset',
            'get_current_url',
            'fn_static',
            'print_r'
        ];

        foreach ($new_functions as $function) {
            $twig->addFunction(
                new \Twig\TwigFunction($function, [$this, $function])
            );
        }

       return $twig;
    }

    /**
     * @param $id
     * @return mixed
     */
    function get_timber_post($id) : mixed {
        return \Timber::get_post($id);
    }

    /**
     * @param $id
     * @param $taxonomy
     * @return mixed
     */
    function get_timber_term($id, $taxonomy) : mixed {
        return \Timber::get_term($id, $taxonomy);
    }

    /**
     * @param $id
     * @param string $size
     * @param array $attr
     * @return mixed
     */
    function wp_get_attachment_image($id, $size = 'thumbnail', $attr = []) : mixed {
        $filename = pathinfo(get_attached_file($id))['filename'];
        if(!array_key_exists('id', $attr)) {
            $attr['id'] = $filename;
        }

        $image = wp_get_attachment_image($id, $size, false, $attr);

        return $this->prepare_lzay_load($image);
    }

    /**
     * @param $id
     * @param string $size
     * @param array $attr
     * @return mixed
     */
    function get_the_post_thumbnail($id, $size = 'thumbnail', $attr = []) : mixed {
        $image = get_the_post_thumbnail($id, $size, $attr);

        return $this->prepare_lzay_load($image);
    }

    /**
     * @return mixed
     */
    function get_the_password_form() : mixed {
        return get_the_password_form();
    }

    /**
     * @param $file
     * @param string $module
     * @return string
     */
    function asset($file, $module = 'rwd') : string {
        return $this->get_filename($module, $file);
    }

    /**
     * @return string
     */
    function get_current_url() : string {
        return \Timber\URLHelper::get_current_url();
    }

    /**
     * @param $class
     * @param $method
     * @param ...$args
     * @return mixed
     * @throws \Exception
     */
    function fn_static($class, $method, ...$args) : mixed {
        if (!class_exists($class)) {
            throw new \Exception("Cannot call static method $method on Class $class: Invalid Class");
        }

        if (!method_exists($class, $method)) {
            throw new \Exception("Cannot call static method $method on Class $class: Invalid method");
        }

        return forward_static_call_array([$class, $method], $args);
    }

    /**
     * @param $content
     * @return string
     */
    function print_r($content) {
        return '<pre>' . print_r($content, true) . '</pre>';
    }
}
