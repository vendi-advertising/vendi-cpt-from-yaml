# Custom Post Types (CPTs)

[![PHP Composer](https://github.com/vendi-advertising/vendi-cpt-from-yaml/actions/workflows/php.yml/badge.svg)](https://github.com/vendi-advertising/vendi-cpt-from-yaml/actions/workflows/php.yml)
[![codecov](https://codecov.io/gh/vendi-advertising/vendi-cpt-from-yaml/branch/master/graph/badge.svg)](https://codecov.io/gh/vendi-advertising/vendi-cpt-from-yaml)

Vendi stores CPT information in [YAML](https://yaml.org/) files and uses this library for parsing and loading
CPTs. Although there is a very slight overhead in doing this, the front-end caching will
offset this completely. For sites that don't have a front-end cache, the YAML file could be additionally
parsed and stored in a transient.

## Location and File Name
The config file is generally stored in a folder off of the theme's root called `.config` with a name of `cpts.yaml`. You
may also create an environment variable called `CPT_YAML_FILE` that points either to the absolute path of the config
file, or a path relative to the theme's directory.

## Options
The file format is very simple and is intended for the 99% use case for CPTs. There are no features in the YAML file
that aren't available to the traditional `register_post_type` WordPress function.
  * The root key is the WordPress slug for the CPT
    * Because this is the slug, just like any other CPT, you should never change this once you've defined it or else
      your existing content might get lost.
  * The two required child keys are `singular` and `plural` which control the
    display name for the CPT.
  * Two optional child keys are `singular_lowercase` and `plural_lowercase`
    however if they are not present they will be generated automatically.
  * The last optional child key is `extended_options` whose values map directly
    to the core WordPress function
    [register_post_type](https://developer.wordpress.org/reference/functions/register_post_type/)

## Sample

```yaml
# The slug for our alert CPTs
alert:
    # Singular and plural human-readable versions
    singular: Alert
    plural: Alerts

    # The contents of this key are merged with the required WordPress labels and passed directly
    # as the second argument to the register_post_type function. Common default values for these
    # items are set in https://github.com/vendi-advertising/vendi-cpt-from-yaml/blob/f85a3db005da11f88b9e5340ba12202b86584f81/src/CPTBase.php#L160
    extended_options:
        supports:
            - title
            - editor
        public: true
        has_archive: false
        menu_icon: dashicons-visibility
        exclude_from_search: true
        publicly_queryable: false
```
