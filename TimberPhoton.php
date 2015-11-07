<?php
/*
Plugin Name: Timber with Jetpack Photon
Plugin URI: http://slimndap.com
Description: Make the Timber plugin work with Jetpack's Photon. Once installed, all TimberImages will use Photon as a CDN and for image manipulation (eg. resize).
Author: Jeroen Schmit
Version: 0.3
Author URI: http://slimndap.com
*/

class TimberPhoton
{
    public function __construct()
    {
        $this->admin_notices = array();
        $this->photon_hosts = array(
            'i0.wp.com',
            'i1.wp.com',
            'i2.wp.com',
        );

        add_action('plugins_loaded', array($this, 'plugins_loaded'));
    }

    /**
     * @param $twig
     *
     * @return mixed
     */
    public function twig_apply_filters($twig)
    {
        $twig->addFilter('resize', new Twig_Filter_Function(array($this, 'resize')));
        $twig->addFilter('fit', new Twig_Filter_Function(array($this, 'fit')));
        $twig->addFilter('letterbox', new Twig_Filter_Function(array($this, 'letterbox')));
        $twig->addFilter('quality', new Twig_Filter_Function(array($this, 'set_quality')));

        return $twig;
    }

    public function admin_notices()
    {
        if (!empty($this->admin_notices)) {
            echo '<div class="error"><p>';
            if (in_array('timber', $this->admin_notices)) {
                _e('Timber with Jetpack Photon requires the Timber plugin to be installed and activated. <a href="http://jarednova.github.io/timber/">Get it here</a>.');
            }
            if (in_array('photon', $this->admin_notices)) {
                _e('Timber with Jetpack Photon requires the Jetpack plugin to be installed with Photon activated.');
            }
            echo '</p></div>';
        }
    }

    /**
     * @see http://developer.wordpress.com/docs/photon/api/#lb
     *
     * @param string $src
     * @param int    $w
     * @param int    $h
     *
     * @return string
     */
    public function letterbox($src, $w, $h)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args = array(
            'lb' => $w.','.$h,
        );

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see http://developer.wordpress.com/docs/photon/api/#resize
     * @see http://developer.wordpress.com/docs/photon/api/#w
     *
     * @param string $src
     * @param int    $w
     * @param int    $h
     *
     * @return string
     */
    public function resize($src, $width, $height = 0)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args = [];

        if (!empty($height)) {
            $args['resize'] = $width . ',' . $height;
        } else {
            $args['w'] = $width;
        }

        $src = add_query_arg($args, $src);

        return $src;
    }


    /**
     * @see http://developer.wordpress.com/docs/photon/api/#fit
     *
     * @param string $src
     * @param int    $w
     * @param int    $h
     *
     * @return string
     */
    public function fit($src, $width, $height)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['fit'] = $width . ',' . $height;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#quality
     *
     * @param string $src
     * @param int    $quality
     *
     * @return string
     */
    public function set_quality($src, $quality = 100)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['quality'] = $quality;

        $src = add_query_arg($args, $src);

        return $src;
    }

    public function plugins_loaded()
    {
        if ($this->system_ready()) {
            add_action('timber/twig/filters', array(&$this, 'twig_apply_filters'), 99);
            add_action('twig_apply_filters', array(&$this, 'twig_apply_filters'), 99);
            add_filter('timber_image_src', array($this, 'timber_image_src'));
        }
    }

    /**
     * Translate a URL to a Photon URL.
     * Photon docs: http://i0.wp.com/$REMOTE_IMAGE_URL.
     *
     * @param $url
     *
     * @return string
     */
    public function photon_url($url)
    {
        if ($parsed = parse_url($url)) {
            if (in_array($parsed['host'], $this->photon_hosts)) {
                // $url is already a Photon URL.
                // Leave it alone.
            } else {
                // Strip http:// from $url.
                $stripped_url = $parsed['host'].$parsed['path'];
                if (!empty($parsed['query'])) {
                    $stripped_url .= '?'.$parsed['query'];
                }

                /*
                 * Pick a Photon host based on the crc32 of the stripped_url.
                 * Photon docs: Multiple domains. In order to take advantage of parallel downloads
                 * we support multiple sub-domains for Photon. If you tend to have many images per
                 * page you can split them across i0.wp.com, i1.wp.com, and i2.wp.com.
                 */
                $photon_host = $this->photon_hosts[abs(crc32($stripped_url) % 2)];

                // Create a Photon URL.
                $url = $parsed['scheme'].'://'.$photon_host.'/'.$stripped_url;
            }
        }

        return $url;
    }

    /*
     * Check if Timber and Jetpack are installed and activated.
     * Check if Photon is activated
     */
    public function system_ready()
    {
        global $timber;

        // Is Timber installed and activated?
        if (!class_exists('Timber')) {
            $this->admin_notices[] = 'timber';
            add_action('admin_notices', array($this, 'admin_notices'));

            return false;
        }

        // Determine if Jetpack is installed and can generate photon URLs.
        if (!class_exists('Jetpack') || !method_exists('Jetpack', 'get_active_modules') || !in_array('photon', Jetpack::get_active_modules())) {
            $this->admin_notices[] = 'photon';
            add_action('admin_notices', array($this, 'admin_notices'));

            return false;
        }

        return true;
    }

    public function timber_image_src($src)
    {
        return $this->photon_url($src);
    }
}

new TimberPhoton();
