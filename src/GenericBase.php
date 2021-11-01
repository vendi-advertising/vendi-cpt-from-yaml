<?php

namespace Vendi\CptFromYaml;

abstract class GenericBase
{
    protected $singular;

    protected $plural;

    protected $singular_lc;

    protected $plural_lc;

    protected $type_name;

    private $extended_options = [];

    public function __construct(string $type_name)
    {
        $this->type_name = $type_name;
    }

    abstract public function make_labels(): array;

    abstract public function get_default_register_args(): array;

    abstract public function get_merged_register_args(): array;

    public function get_extended_options(): array
    {
        return $this->extended_options;
    }

    public function set_extended_options(array $extended_options): void
    {
        $this->extended_options = $extended_options;
    }

    public function _maybe_set_lower_case_versions(): void
    {
        if ($this->has_title_case_singular_name() && !$this->has_lower_case_singular_name()) {
            $this->set_lower_case_singular_name(mb_strtolower($this->get_title_case_singular_name()));
        }

        if ($this->has_title_case_plural_name() && !$this->has_lower_case_plural_name()) {
            $this->set_lower_case_plural_name(mb_strtolower($this->get_title_case_plural_name()));
        }
    }

    public function get_type_name(): string
    {
        return $this->type_name;
    }

    public function set_title_case_singular_name(string $singular): void
    {
        $this->singular = $singular;
        $this->_maybe_set_lower_case_versions();
    }

    public function get_title_case_singular_name(): string
    {
        return $this->singular;
    }

    public function has_title_case_singular_name(): bool
    {
        return isset($this->singular);
    }

    public function set_title_case_plural_name(string $plural): void
    {
        $this->plural = $plural;
        $this->_maybe_set_lower_case_versions();
    }

    public function get_title_case_plural_name(): string
    {
        return $this->plural;
    }

    public function has_title_case_plural_name(): bool
    {
        return isset($this->plural);
    }

    public function set_lower_case_singular_name(string $singular): void
    {
        $this->singular_lc = $singular;
    }

    public function get_lower_case_singular_name(): string
    {
        return $this->singular_lc;
    }

    public function has_lower_case_singular_name(): bool
    {
        return isset($this->singular_lc);
    }

    public function set_lower_case_plural_name(string $plural): void
    {
        $this->plural_lc = $plural;
    }

    public function get_lower_case_plural_name(): string
    {
        return $this->plural_lc;
    }

    public function has_lower_case_plural_name(): bool
    {
        return isset($this->plural_lc);
    }
}