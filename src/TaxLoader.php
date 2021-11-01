<?php

namespace Vendi\CptFromYaml;

use Vendi\YamlLoader\YamlLoaderBaseWithObjectCache;

class TaxLoader extends YamlLoaderBaseWithObjectCache
{
    public function is_config_valid(array $config): bool
    {
        return true;
    }

    /**
     * @return TaxBase[]
     */
    public static function create_objects_from_cpt(string $cpt_slug, array $options): array
    {
        $ret = [];
        if (isset($options['taxonomies'])) {
            foreach ($options['taxonomies'] as $taxonomy_key => $taxonomy_options) {
                $tax = new TaxBase($taxonomy_key, $cpt_slug);

                if (isset($taxonomy_options['singular'])) {
                    $tax->set_title_case_singular_name($taxonomy_options['singular']);
                }

                if (isset($taxonomy_options['plural'])) {
                    $tax->set_title_case_plural_name($taxonomy_options['plural']);
                }

                if (isset($taxonomy_options['singular_lowercase'])) {
                    $tax->set_lower_case_singular_name($taxonomy_options['singular_lowercase']);
                }

                if (isset($taxonomy_options['plural_lowercase'])) {
                    $tax->set_lower_case_plural_name($taxonomy_options['plural_lowercase']);
                }

                if (isset($taxonomy_options['extended_options'])) {
                    $tax->set_extended_options($taxonomy_options['extended_options']);
                }

                $ret[] = $tax;
            }
        }

        return $ret;
    }
}