<?php
namespace Jm\WpHelper\Timber;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper\Helper;


class Filters extends \Jm\WpHelper\WpHelper {
    public function __construct() {

        parent::__construct();

        add_filter('timber/twig', [$this, 'add_filters_to_twig']);
    }

    /**
     * Add Filters
     */
    public function add_filters_to_twig(\Twig\Environment $twig) : \Twig\Environment {

        $new_filters = [
            'prepare_class',
            'column',
            'instance',
            'remove_orphan',
            'shuffle'
        ];

        foreach ($new_filters as $filter) {
            $twig->addFilter(
                new \Twig\TwigFilter($filter, [$this, $filter])
            );

        }

        return $twig;
    }

    /**
     * @param $base
     * @param $class
     * @return string
     */
    public function prepare_class($base, $class) : string {
        return Helper\Html::prepare_class($base, $class);
    }

    /**
     * @param $var
     * @param $column
     * @return array
     */
    public function column($var, $column) : array {
        return array_map(function($e) use ($column) {
            return is_object($e) ? $e->$column : $e[$column]; }, $var);
    }

    /**
     * @param $var
     * @return string
     */
    public function instance($var) : string {
        return is_object($var) ? get_class($var) : '';
    }

    /**
     * @param $base
     * @return string
     */
    public function remove_orphan($base) : string {
        return Helper\Php::remove_orphan($base);
    }

    /**
     * @param $array
     * @return array
     */
    public function shuffle($array) : array {
        shuffle($array);

        return $array;
    }
}
