<?php
/**
 * W3 Total Cache Configuration
 * Optimized for PremierPlug WordPress Platform
 */

return array(
    // Page Cache
    'pgcache.enabled' => true,
    'pgcache.engine' => 'file',
    'pgcache.cache.home' => true,
    'pgcache.cache.feed' => false,
    'pgcache.cache.query' => true,
    'pgcache.cache.ssl' => true,
    'pgcache.reject.logged' => true,
    'pgcache.lifetime' => 3600,

    // Minify
    'minify.enabled' => true,
    'minify.auto' => true,
    'minify.html.enable' => true,
    'minify.css.enable' => true,
    'minify.js.enable' => true,
    'minify.css.method' => 'minify',
    'minify.js.method' => 'minify',

    // Database Cache
    'dbcache.enabled' => true,
    'dbcache.engine' => 'file',
    'dbcache.lifetime' => 3600,

    // Object Cache
    'objectcache.enabled' => false, // Enable if Redis/Memcached available
    'objectcache.engine' => 'file',
    'objectcache.lifetime' => 3600,

    // Browser Cache
    'browsercache.enabled' => true,
    'browsercache.html.etag' => true,
    'browsercache.html.w3tc' => true,
    'browsercache.other.etag' => true,
    'browsercache.other.w3tc' => true,
    'browsercache.cssjs.etag' => true,
    'browsercache.cssjs.w3tc' => true,
    'browsercache.cssjs.compression' => true,
    'browsercache.cssjs.expires' => true,
    'browsercache.cssjs.lifetime' => 31536000,
    'browsercache.html.expires' => true,
    'browsercache.html.lifetime' => 3600,
    'browsercache.other.expires' => true,
    'browsercache.other.lifetime' => 31536000,

    // CDN (disabled by default)
    'cdn.enabled' => false,
    'cdn.engine' => 'cloudflare',

    // Fragment Cache
    'fragment.enabled' => false,

    // Varnish
    'varnish.enabled' => false,

    // Extensions
    'extensions.active' => array(
        'genesis.framework',
        'wpml'
    ),

    // Mobile
    'mobile.enabled' => false,

    // Referrer Groups
    'referrer.enabled' => false,

    // User Agent Groups
    'useragent.enabled' => false,

    // Reverse Proxy
    'pgcache.purge.postpages_limit' => 10,

    // Don't Cache
    'pgcache.reject.uri' => array(
        'wp-admin',
        'wp-login.php',
        'wp-cron.php',
        '/checkout/',
        '/cart/',
        '/my-account/',
        '/admin-ajax.php'
    ),

    'pgcache.reject.cookie' => array(
        'wordpress_logged_in',
        'comment_author',
        'wp_postpass'
    ),

    // Cache Preload
    'pgcache.prime.enabled' => true,
    'pgcache.prime.interval' => 900,
    'pgcache.prime.limit' => 10,
    'pgcache.prime.sitemap' => 'sitemap.xml',

    // Advanced
    'dbcache.reject.sql' => array(),
    'pgcache.debug' => false,
    'minify.debug' => false,
    'dbcache.debug' => false
);
