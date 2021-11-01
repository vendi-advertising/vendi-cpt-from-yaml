<?php

namespace Vendi\CptFromYaml;

final class TaxBase extends GenericBase
{
    /**
     * @var string
     */
    private $post_type;

    public function __construct(string $type_name, string $post_type, ?array $extended_options = [])
    {
        parent::__construct($type_name);
        $this->post_type = $post_type;
        if (count($extended_options)) {
            $this->set_extended_options($extended_options);
        }
    }

    public function make_labels(): array
    {
        $singular = $this->get_title_case_singular_name();
        $plural = $this->get_title_case_plural_name();
        $singular_lc = $this->get_lower_case_singular_name();
        $plural_lc = $this->get_lower_case_plural_name();

        return [
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => "Search $plural",
            'popular_items' => "Popular $plural",
            'all_items' => "All $plural",
            'parent_item' => "Parent $singular_lc",
            'parent_item_colon' => "Parent $singular_lc",
            'edit_item' => "Edit $singular_lc",
            'update_item' => "Update $singular_lc",
            'add_new_item' => "Add New $singular_lc",
            'new_item_name' => "New $singular_lc Name",
            'separate_items_with_commas' => "Separate $plural_lc with commas",
            'add_or_remove_items' => "Add or remove $plural_lc",
            'choose_from_most_used' => "Choose from the most used $plural_lc",
            'not_found' => "No $plural_lc found.",
            'menu_name' => $plural,
        ];
    }

    public function get_default_register_args(): array
    {
        return [
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
        ];
    }

    public function get_merged_register_args(): array
    {
        $local = [
            'description' => $this->get_title_case_singular_name(),
            'labels' => $this->make_labels(),
        ];

        return array_merge($this->get_default_register_args(), $this->get_extended_options(), $local);
    }

    public function set_post_type(string $post_type): self
    {
        $this->post_type = $post_type;

        return $this;
    }

    public function get_post_type(): string
    {
        return $this->post_type;
    }

    public function register_taxonomy(): void
    {
        \register_taxonomy(
            $this->get_type_name(),
            $this->get_post_type(),
            $this->get_merged_register_args()
        );
    }
}