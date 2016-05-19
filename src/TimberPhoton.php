<?php

class TimberPhoton
{
    public function initialize()
    {
        add_action('timber/twig/filters', [&$this, 'twig_apply_filters'], 99);
        add_action('twig_apply_filters', [&$this, 'twig_apply_filters'], 99);
        add_filter('timber_image_src', [$this, 'timber_image_src']);
    }

    /**
     * @param $twig
     *
     * @return mixed
     */
    public function twig_apply_filters($twig)
    {
        $twig->addFilter('crop', new Twig_Filter_Function([$this, 'crop']));
        $twig->addFilter('resize', new Twig_Filter_Function([$this, 'resize']));
        $twig->addFilter('fit', new Twig_Filter_Function([$this, 'fit']));
        $twig->addFilter('lb', new Twig_Filter_Function([$this, 'add_black_letterboxing']));
        $twig->addFilter('ulb', new Twig_Filter_Function([$this, 'remove_black_letterboxing']));
        $twig->addFilter('image_filter', new Twig_Filter_Function([$this, 'apply_image_filter']));
        $twig->addFilter('brightness', new Twig_Filter_Function([$this, 'set_brightness']));
        $twig->addFilter('contrast', new Twig_Filter_Function([$this, 'set_contrast']));
        $twig->addFilter('colorize', new Twig_Filter_Function([$this, 'colorize']));
        $twig->addFilter('smooth', new Twig_Filter_Function([$this, 'smooth']));
        $twig->addFilter('zoom', new Twig_Filter_Function([$this, 'zoom']));
        $twig->addFilter('quality', new Twig_Filter_Function([$this, 'set_quality']));
        $twig->addFilter('strip', new Twig_Filter_Function([$this, 'strip']));

        return $twig;
    }

    /**
     * @see http://developer.wordpress.com/docs/photon/api/#crop
     *
     * @param string $src
     * @param string $x
     * @param string $y
     * @param string $width
     * @param string $height
     *
     * @return string
     */
    public function crop($src, $x, $y, $width, $height)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['crop'] = $x . ',' . $y . ',' . $width . ',' . $height;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see http://developer.wordpress.com/docs/photon/api/#resize
     * @see http://developer.wordpress.com/docs/photon/api/#w
     *
     * @param string $src
     * @param int    $width
     * @param int    $height
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
     * @param int    $width
     * @param int    $height
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
     * @see http://developer.wordpress.com/docs/photon/api/#lb
     *
     * @param string $src
     * @param int    $width
     * @param int    $height
     *
     * @return string
     */
    public function add_black_letterboxing($src, $width, $height)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args = [
            'lb' => $width.','.$height,
        ];

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#uld
     *
     * @param string $src
     *
     * @return string
     */
    public function remove_black_letterboxing($src)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['uld'] = true;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#filter
     *
     * @param string $src
     * @param string $filter
     *
     * @return string
     */
    public function apply_image_filter($src, $filter)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['filter'] = $filter;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#brightness
     *
     * @param string $src
     * @param int $brightness
     *
     * @return string
     */
    public function set_brightness($src, $brightness)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['brightness'] = $brightness;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#contrast
     *
     * @param string $src
     * @param int $brightness
     *
     * @return string
     */
    public function set_contrast($src, $contrast)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['contrast'] = $contrast;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#colorize
     *
     * @param string $src
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return string
     *
     */
    public function colorize($src, $red = 0, $green = 0, $blue = 0)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['colorize'] = $red.','.$green.','.$blue;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#smooth
     *
     * @param string $src
     * @param int $smooth
     *
     * @return string
     *
     */
    public function smooth($src, $smooth)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['smooth'] = $smooth;

        $src = add_query_arg($args, $src);

        return $src;
    }

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#zoom
     *
     * @param string $src
     * @param int|float $zoom
     *
     * @return string
     */
    public function zoom($src, $zoom)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['zoom'] = $zoom;

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

    /**
     * @see https://developer.wordpress.com/docs/photon/api/#strip
     *
     * @param string $src
     * @param string $strip
     *
     * @return string
     *
     */
    public function strip($src, $strip)
    {
        if (empty($src)) {
            return '';
        }

        $src = $this->photon_url($src);

        $args['strip'] = $strip;

        $src = add_query_arg($args, $src);

        return $src;
    }


    /**
     * Translate a URL to a Photon URL.
     * @see https://developer.wordpress.com/docs/photon/
     *
     * @param string $url
     *
     * @return string
     */
    public function photon_url($url)
    {
        $photon_hosts = [
            'i0.wp.com',
            'i1.wp.com',
            'i2.wp.com',
        ];

        $parsed_url = parse_url($url);

        if (in_array($parsed_url['host'], $photon_hosts)) {
            return $url;
        }

        // Strip http:// from $url.
        $stripped_url = $parsed_url['host'] . $parsed_url['path'];

        if (!empty($parsed_url['query'])) {
            $stripped_url .= '?' . $parsed_url['query'];
        }

        /*
         * Pick a Photon host based on the crc32 of the stripped_url.
         * Photon docs: Multiple domains. In order to take advantage of parallel downloads
         * we support multiple sub-domains for Photon. If you tend to have many images per
         * page you can split them across i0.wp.com, i1.wp.com, and i2.wp.com.
         */
        $photon_host = $photon_hosts[abs(crc32($stripped_url) % 2)];

        // Create a Photon URL.
        $url = sprintf(
            '//%s/%s',
            $photon_host,
            $stripped_url
        );

        return $url;
    }

    /**
     * @param string $src
     *
     * @return string
     */
    public function timber_image_src($src)
    {
        return $this->photon_url($src);
    }
}
