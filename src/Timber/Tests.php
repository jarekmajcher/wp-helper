<?php
namespace Jm\WpHelper\Timber;

if (!defined('WPINC')) {
    die;
}

use Jm\WpHelper;


class Tests extends \Jm\WpHelper\WpHelper {
    public function __construct() {

        parent::__construct();

        add_filter('timber/twig', [$this, 'add_tests_to_twig']);
    }

    public function add_tests_to_twig(\Twig\Environment $twig) : \Twig\Environment {

        $new_tests = [
            'instanceof'
        ];

        foreach ($new_tests as $test) {
            $twig->addTest(new \Twig\TwigTest($test, [$this, $test]));
        }

        return $twig;
    }

    /**
     * @param $var
     * @param $instance
     * @return bool
     */
    function instanceof($var, $instance) : bool {
        $class = str_replace('\\', '', get_class($var));

        return $class === $instance;
    }
}
