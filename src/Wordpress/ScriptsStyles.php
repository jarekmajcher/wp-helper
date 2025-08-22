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
        $file = $this->get_asset_filename($n,$file . '.js');
        $filePath = $this->documentRoot . DIRECTORY_SEPARATOR . $file;
        $fileUrl = $this->siteUrl . DIRECTORY_SEPARATOR . $file;

        if(@file_exists($filePath) && 0 != filesize($filePath)) {
            if ($print) {
                echo sprintf('<script>%s</script>', file_get_contents($filePath)) . PHP_EOL;
            } else {
                echo sprintf( '<script src="%s"></script>', $fileUrl ) . PHP_EOL;
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
            wp_enqueue_style('style-' . $style, $filePath, null);
        }

        $scripts = ['admin'];
        foreach($scripts as $script) {
            $fileName = $this->get_asset_filename('admin', $script . '.js');
            $filePath = $this->siteUrl . DIRECTORY_SEPARATOR . $fileName;
            wp_enqueue_script('script-' . $script, $filePath, array('wp-blocks', 'wp-element'));
        }
    }
}
