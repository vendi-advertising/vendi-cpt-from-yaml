<?php

declare(strict_types=1);

if(!function_exists('register_taxonomy')) {
    // This function does not necessarily accurately mock the more complicated taxonomy/CPT relationship,
    // but should be sufficient for testing purposes
    function register_taxonomy(string $taxonomy, array|string $object_type, array|string $args = [])
    {
        global $mock_registered_taxonomies;
        if(!is_array($mock_registered_taxonomies)) {
            $mock_registered_taxonomies = [];
        }
        
        if(!isset($mock_registered_taxonomies[$taxonomy])){
            $mock_registered_taxonomies[$taxonomy] = [];
        }
        
        if(!is_array($object_type)){
            $object_type = [$object_type];
        }
        foreach($object_type as $name){
            $mock_registered_taxonomies[$taxonomy][$name] = $args;
        }
    }
}

if (!function_exists('register_post_type')) {
    function register_post_type(string $post_type, array|string $args = [])
    {
        global $mock_registered_post_types;
        if(!is_array($mock_registered_post_types)) {
            $mock_registered_post_types = [];
        }
        
        $mock_registered_post_types[$post_type] = $args;
    }
}

if (!function_exists('get_template_directory')) {
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

if(!function_exists('apply_filters')) {
    function apply_filters(string $hook_name, mixed $value, mixed ...$args)
    {
        global $hooks;
        foreach($hooks[$hook_name] ?? [] as $callback)
        {
            $value = $callback($value, ...$args);
        }
    
        return $value;
    }
}

if(!function_exists('add_filter')) {
    // This mock ignores priority completely
    function add_filter(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1)
    {
        global $hooks;
        if(!is_array($hooks))
        {
            $hooks = [];
        }
        if(!isset($hooks[$hook_name]))
        {
            $hooks[$hook_name] = [];
        }
        $hooks[$hook_name][] = $callback;
    }
}

if (!function_exists('wp_cache_get')) {
    function wp_cache_get($key, $group = '', $force = false, &$found = null)
    {
        global $wp_object_cache;
        if (!is_object($wp_object_cache)) {
            $wp_object_cache = create_object_cache();
        }
        return $wp_object_cache->get($key, $group, $force, $found);
    }
}

if (!function_exists('wp_cache_set')) {
    function wp_cache_set($key, $data, $group = '', $expire = 0)
    {
        global $wp_object_cache;
        if (!is_object($wp_object_cache)) {
            $wp_object_cache = create_object_cache();
        }
        return $wp_object_cache->set($key, $data, $group, (int)$expire);
    }
}

if (!function_exists('wp_cache_delete')) {
    function wp_cache_delete($key, $group = '')
    {
        global $wp_object_cache;
        if (!is_object($wp_object_cache)) {
            $wp_object_cache = create_object_cache();
        }
        return $wp_object_cache->delete($key, $group);
    }
}

function create_object_cache(): object
{
    return new class() {
        private $cache = [];
        public $cache_hits = 0;
        public $cache_misses = 0;

        public function get($key, $group = 'default', $force = false, &$found = null)
        {
            if (empty($group)) {
                $group = 'default';
            }

            if ($this->_exists($key, $group)) {
                $found = true;
                $this->cache_hits++;
                if (is_object($this->cache[$group][$key])) {
                    return clone $this->cache[$group][$key];
                }

                return $this->cache[$group][$key];
            }

            $found = false;
            $this->cache_misses++;
            return false;
        }

        public function set($key, $data, $group = 'default', $expire = 0): bool
        {
            if (empty($group)) {
                $group = 'default';
            }

            if (is_object($data)) {
                $data = clone $data;
            }

            $this->cache[$group][$key] = $data;
            return true;
        }

        public function delete($key, $group = 'default', $deprecated = false): bool
        {
            if (empty($group)) {
                $group = 'default';
            }

            if (!$this->_exists($key, $group)) {
                return false;
            }

            unset($this->cache[$group][$key]);
            return true;
        }

        protected function _exists($key, $group): bool
        {
            return isset($this->cache[$group]) && (isset($this->cache[$group][$key]) || array_key_exists($key, $this->cache[$group]));
        }
    };
}
