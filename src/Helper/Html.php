<?php
namespace Jm\WpHelper\Helper;

use DOMDocument;
use DOMXPath;

/**
 */
class Html
{
    /**
     * @param $content
     * @param $attrs
     * @return string
     */
    public static function add_attribute($content, $attrs = []) : string {
        $dom = new DOMDocument();
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        foreach($attrs as $path => $attr) {
            $xpath = new DOMXPath($dom);
            $tags = $xpath->evaluate('//' . $path);

            foreach ($tags as $tag) {

                foreach($attr as $k => $v) {
                    $v = ($tag->getAttribute($k)) ? $tag->getAttribute($k) . ' ' . $v : $v;

                    $tag->setAttribute($k, $v);
                }

                $content = $dom->saveHTML();
            }
        }
        return $content;
    }

    /**
     * @param $attributes
     * @return string
     */
    public static function prepare_attributes($attributes = []) : string {
        $output = '';

        foreach($attributes as $attribute) {
            if(array_key_exists('name', $attribute) && array_key_exists('value', $attribute)) {
                $output .= sprintf("%s=\"%s\"", $attribute['name'], $attribute['value']) . ' ';
            }
        }
        return $output;
    }

    /**
     * @param $main
     * @param $string
     * @return string
     */
    public static function prepare_class($main, $string) : string {
        $ret = $main;

        if($string != '') {
            $classes = explode(' ', $string);
            sort($classes);
            foreach($classes as $class) {
                if(substr($class, 0, 2) == '--') {
                    $ret .= ' ' . $main . $class;
                }
                else {
                    $ret .= ' ' . $class;
                }
            }
        }
        return $ret;
    }
}


