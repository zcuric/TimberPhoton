Timber Jetpack Photon Image Extension
============

Make the [Timber](https://wordpress.org/plugins/timber-library/) plugin work with Jetpack's Photon. Once installed, all TimberImages use Photon as a CDN and for image manipulation (eg. resize).

[Photon](http://jetpack.me/support/photon/) is an image acceleration and modification service for Jetpack-connected WordPress sites. Converted images are cached automatically and served from the WordPress.com CDN. Photon is part of the Jetpack plugin and completely free.

You can find Timber Jetpack Photon Image Extension in the [Wordpress plugin repository](https://wordpress.org/plugins/timber-with-jetpack-photon/).

## What does it do?

Timber with Jetpack Photon extends the current TimberImage class to use Photon to serve and manipulate your images:

* `{{post.thumbnail.src}}` returns a Photon URL
* `{{post.thumbnail.src|resize(100)}}` returns a Photon URL
* `{{post.thumbnail.src|resize(100,200)}}` returns a Photon URL

Other available filters:

* `crop`
* `fit`
* `lb`
* `ulb`
* `image_filter`
* `brightness`
* `contrast`
* `colorize`
* `smooth`
* `zoom`
* `quality`
* `strip`

Checkout [Photon API documentation](https://developer.wordpress.com/docs/photon/) for more information.
