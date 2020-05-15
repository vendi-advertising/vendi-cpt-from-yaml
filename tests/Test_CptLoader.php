<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\CptFromYaml\CPTBase;
use Vendi\CptFromYaml\CptLoader;
use Vendi\YamlLoader\YamlLoaderBase;
use Vendi\YamlLoader\YamlLoaderBaseWithObjectCache;
use Webmozart\PathUtil\Path;

class Test_CptLoader extends test_base
{
    public function get_simple_mock(string $envVariableForFile = null, string $defaultFileName = null, string $cacheKey = null): YamlLoaderBase
    {
        if (!$envVariableForFile) {
            $envVariableForFile = 'CPT_YAML_FILE';
        }

        if (!$defaultFileName) {
            $defaultFileName = 'test-config.yaml';
        }

        if (!$cacheKey) {
            $cacheKey = 'test-cache-key';
        }

        return new class ($envVariableForFile, $defaultFileName, $cacheKey) extends YamlLoaderBaseWithObjectCache {

            public function is_config_valid(array $config): bool
            {
                return true;
            }

            public function get_env_key(): string
            {
                return $this->envVariableForFile;
            }

            public function get_protected_variable(string $var)
            {
                return $this->$var;
            }

        };
    }

    /**
     * @covers \Vendi\CptFromYaml\CptLoader::create_cpt_objects
     */
    public function test__create_cpt_objects(): void
    {
        $key = $this->get_simple_mock()->get_env_key();

        global $current_test_dir;
        $current_test_dir = '/cheese/';

        $file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.yaml'));
        \putenv("${key}=${file}");
        \touch($file);
        file_put_contents(
            $file,
            <<<'TAG'
alert:
    singular: Alert
    plural: Alerts
TAG
        );

        $cpts = (new CptLoader())->create_cpt_objects();
        $this->assertCount(1, $cpts);
        $this->assertArrayHasKey('alert', $cpts);

        /* @var CPTBase $alert */
        $alert = $cpts['alert'];
        $this->assertInstanceOf(CPTBase::class, $alert);
        $this->assertSame('Alert', $alert->get_title_case_singular_name());
        $this->assertSame('Alerts', $alert->get_title_case_plural_name());
        $this->assertSame('alert', $alert->get_type_name());

        \putenv('CPT_YAML_FILE');
        unlink($file);
    }
}
