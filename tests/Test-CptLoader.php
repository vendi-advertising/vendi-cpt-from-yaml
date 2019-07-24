<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\CptFromYaml\CptLoader;
use Webmozart\PathUtil\Path;

class Test_CptLoader extends test_base
{
    /**
     * @covers \Vendi\CptFromYaml\CptLoader::get_env
     */
    public function test__get_env()
    {
        $this->assertFalse(\getenv('CPT_YAML_FILE'));
        $this->assertSame('', (new CptLoader())->get_env('CPT_YAML_FILE'));
        \putenv('CPT_YAML_FILE=cheese');
        $this->assertSame('cheese', (new CptLoader())->get_env('CPT_YAML_FILE'));
    }

    /**
     * @covers \Vendi\CptFromYaml\CptLoader::get_media_dir
     */
    public function test__get_media_dir()
    {
        global $current_test_dir;
        $current_test_dir = '/cheese/';
        $this->assertSame('/cheese', ((new CptLoader())->get_media_dir()));
    }

    /**
     * @covers \Vendi\CptFromYaml\CptLoader::get_cpt_yaml_file
     */
    public function test__get_cpt_yaml_file()
    {
        global $current_test_dir;
        $current_test_dir = '/cheese/';
        $this->assertSame('/cheese/.config/cpts.yaml', ((new CptLoader())->get_cpt_yaml_file()));
    }

    /**
     * @covers \Vendi\CptFromYaml\CptLoader::get_cpt_yaml_file
     */
    public function test__get_cpt_yaml_file__from_env()
    {
        global $current_test_dir;
        $current_test_dir = '/cheese/';

        //This is absolute and will ignore the media_dir
        \putenv('CPT_YAML_FILE=/tmp/test.yaml');
        $this->assertSame('/tmp/test.yaml', ((new CptLoader())->get_cpt_yaml_file()));

        //This is relative and will use the media_dir
        \putenv('CPT_YAML_FILE=./tmp/test.yaml');
        $this->assertSame('/cheese/tmp/test.yaml', ((new CptLoader())->get_cpt_yaml_file()));
    }

    /**
     * @covers \Vendi\CptFromYaml\CptLoader::get_cpt_yaml_file
     */
    public function test__get_cpt_yaml_file__exists()
    {
        $file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.yaml'));
        \putenv("CPT_YAML_FILE=${file}");
        \touch($file);

        $this->assertSame($file, ((new CptLoader())->get_cpt_yaml_file()));
    }
}
