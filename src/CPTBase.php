<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml;

final class CPTBase
{
    private $singular;

    private $plural;

    private $singular_lc;

    private $plural_lc;

    private $type_name;

    private $extended_options = [];

    public function __construct(string $type_name)
    {
        $this->type_name = $type_name;
    }

    public function _maybe_set_lower_case_versions()
    {
        if ($this->has_title_case_singular_name() && ! $this->has_lower_case_singular_name()) {
            $this->set_lower_case_singular_name(mb_strtolower($this->get_title_case_singular_name()));
        }

        if ($this->has_title_case_plural_name() && ! $this->has_lower_case_plural_name()) {
            $this->set_lower_case_plural_name(mb_strtolower($this->get_title_case_plural_name()));
        }
    }

    public function get_extended_options() : array
    {
        return $this->extended_options;
    }

    public function set_extended_options(array $extended_options)
    {
        $this->extended_options = $extended_options;
    }

    public function get_type_name() : string
    {
        return $this->type_name;
    }

    public function set_title_case_singular_name(string $singular)
    {
        $this->singular = $singular;
        $this->_maybe_set_lower_case_versions();
    }

    public function get_title_case_singular_name() : string
    {
        return $this->singular;
    }

    public function has_title_case_singular_name() : bool
    {
        return isset($this->singular);
    }

    public function set_title_case_plural_name(string $plural)
    {
        $this->plural = $plural;
        $this->_maybe_set_lower_case_versions();
    }

    public function get_title_case_plural_name() : string
    {
        return $this->plural;
    }

    public function has_title_case_plural_name() : bool
    {
        return isset($this->plural);
    }

    public function set_lower_case_singular_name(string $singular)
    {
        $this->singular_lc = $singular;
    }

    public function get_lower_case_singular_name() : string
    {
        return $this->singular_lc;
    }

    public function has_lower_case_singular_name() : bool
    {
        return isset($this->singular_lc);
    }

    public function set_lower_case_plural_name(string $plural)
    {
        $this->plural_lc = $plural;
    }

    public function get_lower_case_plural_name() : string
    {
        return $this->plural_lc;
    }

    public function has_lower_case_plural_name() : bool
    {
        return isset($this->plural_lc);
    }

    public function register_post_type()
    {
        \register_post_type(
                            $this->get_type_name(),
                            $this->get_merged_register_args()
                        );
    }

    public function make_labels() : array
    {
        $singular = $this->get_title_case_singular_name();
        $plural = $this->get_title_case_plural_name();
        $singular_lc = $this->get_lower_case_singular_name();
        $plural_lc = $this->get_lower_case_plural_name();

        return [
                'name'                  => $plural,
                'singular_name'         => $singular,
                'menu_name'             => $plural,
                'name_admin_bar'        => $singular,
                'archives'              => "$singular Archives",
                'attributes'            => "$singular Attributes",
                'parent_item_colon'     => "Parent $singular:",
                'all_items'             => "All $plural",
                'add_new_item'          => "Add New $singular",
                'add_new'               => 'Add New',
                'new_item'              => "New $singular",
                'edit_item'             => "Edit $singular",
                'update_item'           => "Update $singular",
                'view_item'             => "View $singular",
                'view_items'            => "View $singular",
                'search_items'          => "Search $singular",
                'not_found'             => 'Not found',
                'not_found_in_trash'    => 'Not found in Trash',
                'featured_image'        => 'Featured Image',
                'set_featured_image'    => 'Set featured image',
                'remove_featured_image' => 'Remove featured image',
                'use_featured_image'    => 'Use as featured image',
                'insert_into_item'      => "Insert into $singular_lc",
                'uploaded_to_this_item' => "Uploaded to this $singular_lc",
                'items_list'            => "$singular list",
                'items_list_navigation' => "$singular list navigation",
                'filter_items_list'     => "Filter $plural_lc list",
        ];
    }

    public function get_default_register_args() : array
    {
        return [
                'can_export'            => true,
                'capability_type'       => 'page',
                'exclude_from_search'   => false,
                'has_archive'           => false,
                'hierarchical'          => false,
                'menu_position'         => 5,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_in_admin_bar'     => true,
                'show_in_menu'          => true,
                'show_in_nav_menus'     => true,
                'show_in_rest'          => false,
                'show_ui'               => true,
                'supports'              => ['title'],
        ];
    }

    public function get_merged_register_args() : array
    {
        $local = [
                'description' => $this->get_title_case_singular_name(),
                'label'       => $this->get_title_case_singular_name(),
                'labels'      => $this->make_labels(),
        ];

        return array_merge($this->get_default_register_args(), $this->get_extended_options(), $local);
    }
}
