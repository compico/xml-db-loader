<?php

include_once("../classes/AutoCatalog.php");
include_once("../vendor/phpunit/phpunit/src/Framework/TestCase.php");

use PHPUnit\FrameWork\TestCase;

class TestAutoCatalog extends TestCase
{
    public function test_construct()
    {
        $test_id_array_data = array(359039, 402501);
        $test_xml = new AutoCatalog("../test_data/data1.xml", true);
        $id_array = $test_xml->get_id_array();
        $this->assertSame(0, count(array_diff($id_array, $test_id_array_data)));
    }
}

