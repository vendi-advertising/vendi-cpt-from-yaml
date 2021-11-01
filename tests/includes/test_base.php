<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml\tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Vendi\YamlLoader\YamlLoaderBase;
use Vendi\YamlLoader\YamlLoaderBaseWithObjectCache;

/**
 * @coversNothing
 */
class test_base extends TestCase
{
    //This is name of our FS root for testing
    private $_test_root_name = 'vendi-cpt-loader-test';

    //This is an instance of the Virtual File System
    private $_root;

    public function get_vfs_root(): vfsStreamDirectory
    {
        if (!$this->_root) {
            $this->_root = vfsStream::setup(
                $this->get_root_dir_name_no_trailing_slash(),
                null,
                []
            );
        }
        return $this->_root;
    }

    public function get_root_dir_name_no_trailing_slash(): string
    {
        return $this->_test_root_name;
    }

    public function setUp(): void
    {
        global $current_test_dir;
        $current_test_dir = null;
        $this->get_vfs_root();
        $this->reset_env();
    }

    public function tearDown(): void
    {
        global $current_test_dir;
        $current_test_dir = null;
        $this->reset_env();
    }

    private function reset_env(): void
    {
        \putenv('CPT_YAML_FILE');
    }

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
}
