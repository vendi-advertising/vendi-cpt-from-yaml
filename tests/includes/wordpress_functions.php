<?php

declare(strict_types=1);

if (! function_exists('get_template_directory')) {
    function get_template_directory()
    {
        global $current_test_dir;
        return $current_test_dir;
    }
}

if (!function_exists('untrailingslashit')) {
    function untrailingslashit($string)
    {
        return rtrim($string, '/\\');
    }
}

if (!function_exists('wp_cache_get')) {
    function wp_cache_get($key, $group = '', $force = false, &$found = null)
    {
        global $wp_object_cache;
        return $wp_object_cache->get($key, $group, $force, $found);
    }
}

if (!function_exists('wp_cache_set')) {
    function wp_cache_set($key, $data, $group = '', $expire = 0)
    {
        global $wp_object_cache;
        return $wp_object_cache->set($key, $data, $group, (int) $expire);
    }
}

if (!function_exists('wp_cache_delete')) {
    function wp_cache_delete($key, $group = '')
    {
        global $wp_object_cache;
        return $wp_object_cache->delete($key, $group);
    }
}
