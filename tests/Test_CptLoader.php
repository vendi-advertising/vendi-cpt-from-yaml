<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\CptFromYaml\CPTBase;
use Vendi\CptFromYaml\CptLoader;

class Test_CptLoader extends test_base
{
    /**
     * @covers \Vendi\CptFromYaml\CptLoader::create_cpt_objects
     */
    public function test__create_cpt_objects(): void
    {
        $key = $this->get_simple_mock()->get_env_key();

        global $current_test_dir;
        $current_test_dir = '/cheese/';

        $file = vfsStream::url($this->get_root_dir_name_no_trailing_slash() . '/' . 'entry.yaml');
        \putenv("${key}=${file}");
        \touch($file);
        file_put_contents(
            $file,
            <<<'TAG'
alert:
    singular: Alert
    plural: Alerts
    taxonomies:
        new-category: 
            singular: Category
            plural: Categories
            extended_options:
                hierarchical: true
TAG
        );

        $cpts = (new CptLoader())->create_cpt_objects();
        $this->assertCount(1, $cpts);
        $this->assertArrayHasKey('alert', $cpts);

        $alert = $cpts['alert'];
        $this->assertInstanceOf(CPTBase::class, $alert);
        $this->assertSame('Alert', $alert->get_title_case_singular_name());
        $this->assertSame('Alerts', $alert->get_title_case_plural_name());
        $this->assertSame('alert', $alert->get_type_name());

        $this->assertCount(1, $alert->get_taxonomies());

        \putenv('CPT_YAML_FILE');
        unlink($file);
    }
}
