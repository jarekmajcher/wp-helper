<?php
namespace Jm\WpHelper\Wordpress;

if (!defined('WPINC')) {
    die;
}

use \Jm\WpHelper\Helper;

class ScriptsStyles extends \Jm\WpHelper\WpHelper {

    public function __construct() {

        parent::__construct();

        add_action('wp_head', [$this, 'load_rwd_header']);
        add_action('wp_footer', [$this, 'load_rwd_footer']);
        add_action('admin_head', [$this, 'load_admin']);
    }

    /**
     * @param $n
     * @param $file
     * @param $print
     * @return void
     */
    private function style($n, $file, $print) : void {
        $file = $this->get_asset_filename($n,$file . '.css');
        $filePath = $this->documentRoot . DIRECTORY_SEPARATOR . $file;
        $fileUrl = $this->siteUrl . DIRECTORY_SEPARATOR . $file;

        if(@file_exists($filePath) && 0 != filesize($filePath)) {
            if ($print) {
                echo sprintf('<style>%s</style>', file_get_contents($filePath)) . PHP_EOL;
            }
            else {
                echo sprintf( '<link rel="stylesheet" type="text/css"  href="%s" />', $fileUrl ) . PHP_EOL;
            }
        }
    }

    /**
     * @param $n
     * @param $file
     * @param $print
     * @return void
     */
    private function script($n, $file, $print) : void {
        $filePath = $this->get_asset_filename($n,$file . '.js');
        $filePathAbsolute = $this->documentRoot . DIRECTORY_SEPARATOR . $filePath;

        if(@file_exists($filePathAbsolute) && 0 != filesize($filePathAbsolute)) {
            if ($print) {
                echo sprintf('<script>%s</script>', file_get_contents($filePathAbsolute)) . PHP_EOL;
            } else {
                wp_enqueue_script('script-' . $n . '-' . $file, $filePath, [], filemtime($filePathAbsolute));
            }
        }
    }

    /**
     * @return void
     */
    public function load_rwd_header() : void {
        $styles = ['main'];
        foreach($styles as $style) {
            $this->style('rwd', $style, true);
        }

        $scripts = ['runtime', 'main'];
        foreach($scripts as $script) {
            $this->script('rwd', $script, true);
        }
    }

    /**
     * @return void
     */
    public function load_rwd_footer() : void {
        global $post;

        $template = get_page_template_slug();
        $template = $template === '' || $template === null ? false : str_replace('.php', '', $template);

        $styles = ['other'];
        if(get_post_type()) {
            $styles[] = 'single-' . get_post_type();
        }
        if($template !== false) {
            $styles[] = $template;
        }
        $styles = array_merge($styles, Helper\Acf::get_post_assets('css'));

        foreach($styles as $style) {
            $this->style('rwd', $style, false);
        }

        $scripts = ['other'];
        if($template !== false) {
            $scripts[] = $template;
        }
        if(get_post_type()) {
            $scripts[] = 'single-' . get_post_type();
        }
        $scripts = array_merge($scripts, Helper\Acf::get_post_assets('js'));

        foreach($scripts as $script) {
            $this->script('rwd', $script, false);
        }
    }

    /**
     * @return void
     */
    public function load_admin() : void {
        $styles = ['admin'];
        foreach($styles as $style) {
            $fileName = $this->get_asset_filename('admin', $style . '.css');
            $filePath = $this->siteUrl . DIRECTORY_SEPARATOR . $fileName;
            $filePathAbsolute = $this->documentRoot . DIRECTORY_SEPARATOR . $fileName;
            if(@file_exists($filePathAbsolute) && 0 != filesize($filePathAbsolute)) {
                wp_enqueue_style('style-' . $style, $filePath, null, filemtime($filePathAbsolute));
            }
        }

        $scripts = ['admin'];
        foreach($scripts as $script) {
            $fileName = $this->get_asset_filename('admin', $script . '.js');
            $filePath = $this->siteUrl . DIRECTORY_SEPARATOR . $fileName;
            $filePathAbsolute = $this->documentRoot . DIRECTORY_SEPARATOR . $fileName;
            if(@file_exists($filePathAbsolute) && 0 != filesize($filePathAbsolute)) {
                wp_enqueue_script('script-' . $script, $filePath, ['wp-blocks', 'wp-element'], filemtime($filePathAbsolute));
            }
        }

        $runtimeFiles = ['admin'];
        foreach($runtimeFiles as $file) {
            $runtimeFilePath = $this->get_asset_filename($file, 'runtime.js');
            $runtimeFilePathAbsolute = $this->documentRoot . DIRECTORY_SEPARATOR . $runtimeFilePath;

            if(@file_exists($runtimeFilePathAbsolute) && 0 != filesize($runtimeFilePathAbsolute)) {
                echo sprintf('<script>%s</script>', file_get_contents($runtimeFilePathAbsolute)) . PHP_EOL;
            }
        }
    }
}
