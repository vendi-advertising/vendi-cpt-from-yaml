<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml;

use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

final class CptLoader
{
    public function get_env(string $name) : string
    {
        $ret = \getenv($name);
        if (false === $ret) {
            return '';
        }
        return $ret;
    }

    public function get_media_dir() : string
    {
        if (!$this->_media_dir) {
            $this->_media_dir = \untrailingslashit(\get_template_directory());
        }

        return $this->_media_dir;
    }

    public function get_cpt_yaml_file() : string
    {
        $file = $this->get_env('CPT_YAML_FILE');

        if($file){
            //I don't like this but Path::isAbsolute doesn't support stream wrappers
            if (\is_file($file)) {
                return $file;
            }

            //makeAbsolute doesn't work against streams, apparently
            return Path::makeAbsolute($file, $this->get_media_dir());
        }

        //This is the default
        return Path::join($this->get_media_dir() . '/.config/cpts.yaml');
    }

    public static function get_config() : array
    {
        $ret = wp_cache_get('cpt-config');
        if(!is_array($ret)){
            try{
                $ret = Yaml::parseFile($this->get_cpt_yaml_file());
                wp_cache_set('cpt-config', $ret);
            }catch(\Exception $ex){
                $ret = [];
                wp_cache_delete('cpt-config');
            }
        }

        return $ret;
    }

    public static function create_cpt_objects() : array
    {
        $ret = [];
        $config = self::get_config();
        foreach($config as $slug => $options) {
            $cpt = new CPTBase($slug);

            if(isset($options['singular'])){
                $cpt->set_title_case_singular_name($options['singular']);
            }

            if(isset($options['plural'])){
                $cpt->set_title_case_plural_name($options['plural']);
            }

            if(isset($options['singular_lowercase'])){
                $cpt->set_lower_case_singular_name($options['singular_lowercase']);
            }

            if(isset($options['plural_lowercase'])){
                $cpt->set_lower_case_plural_name($options['plural_lowercase']);
            }

            if(isset($options['extended_options'])){
                $cpt->set_extended_options($options['extended_options']);
            }

            $ret[$slug] = $cpt;
        }

        return $ret;
    }

    public static function register_all_cpts()
    {
        $cpts = self::create_cpt_objects();
        foreach($cpts as $cpt){
            $cpt->register_post_type();
        }
    }
}
