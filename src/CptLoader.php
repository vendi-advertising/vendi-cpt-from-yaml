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

    /**
     * @return CPTBase[]
     */
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

            $taxonomies = TaxLoader::create_objects_from_cpt($slug, $options);
            if (count($taxonomies)) {
                $cpt->set_taxonomies($taxonomies);
            }

            $ret[$slug] = $cpt;
        }

        return $ret;
    }

    public static function register_all_cpts(bool $include_taxonomies = false): void
    {
        $obj = new self();
        $cpts = $obj->create_cpt_objects();
        foreach ($cpts as $cpt) {
            // Add a filter so that specific CPTs can be opted out if needed
            if (!\apply_filters('vendi/cpt-loader/should-register-post-type', true, $cpt)) {
                continue;
            }
            $cpt->register_post_type();

            if ($include_taxonomies && $cpt->has_taxonomies()) {
                foreach ($cpt->get_taxonomies() as $taxonomy) {
                    // Add a filter so that specific taxonomies and CPT can be opted out if needed
                    if (!\apply_filters('vendi/cpt-loader/should-register-taxonomy', true, $cpt, $taxonomy)) {
                        continue;
                    }
                    $taxonomy->register_taxonomy();
                }
            }
        }
    }

    public function is_config_valid(array $config): bool
    {
        return true;
    }
}
