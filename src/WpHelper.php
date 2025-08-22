<?php
namespace Jm\WpHelper;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

if (!defined('WPINC')) {
    die;
}

class WpHelper {
    protected $themePath;
    protected $themeUrl;
    protected $themeRelativeUrl;
    protected $documentRoot;
    protected $siteUrl;
    protected $manifest;

    public function __construct() {
        $this->themePath = get_stylesheet_directory();
        $this->themeUrl = get_stylesheet_directory_uri();
        $this->themeRelativeUrl = substr(parse_url($this->themeUrl, PHP_URL_PATH), 1);
        $this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $this->siteUrl = get_site_url();
    }

    /**
     * @param $n
     * @param $file
     * @return string
     */
    protected function get_asset_filename($n, $file) : string {

        $manifest = $this->themePath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $n . DIRECTORY_SEPARATOR . 'manifest.json';
        $package = new Package(new JsonManifestVersionStrategy($manifest));

        return  $package->getUrl( $this->themeRelativeUrl . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $n . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * @param $image
     * @return string
     */
    protected function prepare_lzay_load($image) : string {
        if((strpos($image, 'lazyload') !== false)) {
            $image = str_replace(['src=', 'srcset=', 'sizes='], ['data-src=', 'data-srcset=', 'data-sizes='], $image);
        }

        return $image;
    }
}
