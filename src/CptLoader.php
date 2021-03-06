<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml;

use Vendi\YamlLoader\YamlLoaderBaseWithObjectCache;

final class CptLoader extends YamlLoaderBaseWithObjectCache
{
    public function __construct()
    {
        parent::__construct('CPT_YAML_FILE', 'cpts.yaml', 'cpt-config');
    }

    public function create_cpt_objects(): array
    {
        $ret = [];
        $config = $this->get_config();
        foreach ($config as $slug => $options) {
            $cpt = new CPTBase($slug);

            if (isset($options['singular'])) {
                $cpt->set_title_case_singular_name($options['singular']);
            }

            if (isset($options['plural'])) {
                $cpt->set_title_case_plural_name($options['plural']);
            }

            if (isset($options['singular_lowercase'])) {
                $cpt->set_lower_case_singular_name($options['singular_lowercase']);
            }

            if (isset($options['plural_lowercase'])) {
                $cpt->set_lower_case_plural_name($options['plural_lowercase']);
            }

            if (isset($options['extended_options'])) {
                $cpt->set_extended_options($options['extended_options']);
            }

            $ret[$slug] = $cpt;
        }

        return $ret;
    }

    public static function register_all_cpts(): void
    {
        $obj = new self();
        $cpts = $obj->create_cpt_objects();
        foreach ($cpts as $cpt) {
            $cpt->register_post_type();
        }
    }

    public function is_config_valid(array $config): bool
    {
        return true;
    }
}
