<?php

declare(strict_types=1);

namespace Vendi\CptFromYaml\tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class test_base extends TestCase
{
    //This is name of our FS root for testing
    private $_test_root_name = 'vendi-cpt-loader-test';

    //This is an instance of the Virtual File System
    private $_root;

    public function get_vfs_root()
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

    public function get_root_dir_name_no_trailing_slash()
    {
        return $this->_test_root_name;
    }

    public function setUp()
    {
        global $current_test_dir;

        $current_test_dir = null;

        $this->get_vfs_root();

        $this->reset_env();
    }

    public function tearDown()
    {
        global $current_test_dir;

        $current_test_dir = null;

        $this->reset_env();
    }

    private function reset_env()
    {
        \putenv('CPT_YAML_FILE');
    }
}
