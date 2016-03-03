<?php

use Mjarestad\Filtry\Filtry;

class FiltryTest extends \PHPUnit_Framework_TestCase
{
	public $filter;

	public function setUp()
	{
		$this->filtry = new Filtry;
	}

	public function testTrim()
	{
		$data = $this->filtry->trim(' test ');
		$this->assertEquals('test', $data);
	}

	public function testLtrim()
	{
		$data = $this->filtry->ltrim(' test');
		$this->assertEquals('test', $data);
	}

	public function testRtrim()
	{
		$data = $this->filtry->rtrim('test ');
		$this->assertEquals('test', $data);
	}

	public function testStrToLower()
	{
		$data = $this->filtry->lower('TEST');
		$this->assertEquals('test', $data);
	}

	public function testStrToUpper()
	{
		$data = $this->filtry->upper('test');
		$this->assertEquals('TEST', $data);
	}

	public function testUcFirst()
	{
		$data = $this->filtry->ucfirst('test');
		$this->assertEquals('Test', $data);
	}

	public function testUcWords()
	{
		$data = $this->filtry->ucwords('test some more');
		$this->assertEquals('Test Some More', $data);
	}

	public function testStripSlashes()
	{
		$data = $this->filtry->stripslashes('\test');
		$this->assertEquals('test', $data);
	}

	public function testXssClean()
	{
		$data = $this->filtry->xssClean('<script>eval("test");</script>');
		$this->assertEquals('&lt;script&gt;eval(&quot;test&quot;);&lt;/script&gt;', $data);
	}

	public function testRemoveWhitespace()
	{
		$data = $this->filtry->removeWhitespace('test some more');
		$this->assertEquals('testsomemore', $data);
	}

	public function testChangeNameToRemoveWhitespaceToStripWhitespaces()
	{
		$data = $this->filtry->stripWhitespaces('test some more');
		$this->assertEquals('testsomemore', $data);
	}

	public function testSlug()
	{
		$data = $this->filtry->slug('test Some MORE');
		$this->assertEquals('test-some-more', $data);
	}

	public function testPrepUrl()
	{
		$data = $this->filtry->prepUrl('www.test.com');
		$this->assertEquals('http://www.test.com', $data);
	}

	public function testCamelCase()
	{
		$data = $this->filtry->camelCase('test_camel_case');
		$this->assertEquals('testCamelCase', $data);
	}

	public function testSnakeCase()
	{
		$data = $this->filtry->snakeCase('testSnakeCase');
		$this->assertEquals('test_snake_case', $data);
	}

	public function testStudlyCase()
	{
		$data = $this->filtry->studlyCase('test_studly_case');
		$this->assertEquals('TestStudlyCase', $data);
	}

	public function testGetOld()
	{
		$filter = $this->filtry->make(array('attribute' => ' test get filtered'), array('attribute' => 'trim|ucwords'));
		$data = $filter->getOld();
		$this->assertEquals(' test get filtered', $data['attribute']);
	}

	public function testGetFiltered()
	{
		$filter = $this->filtry->make(array('attribute' => ' test get filtered'), array('attribute' => 'trim|ucwords'));
		$data = $filter->getFiltered();
		$this->assertEquals('Test Get Filtered', $data['attribute']);
	}

	public function testCreateCustomFilter()
	{
		$this->filtry->extend('custom_filter', function(){
			return 'test-string';
		});
		$data = $this->filtry->customFilter('some data');
		$this->assertEquals('test-string', $data);
	}

	public function testMakeWithCustomFilter()
	{
		$this->filtry->extend('custom_filter', function(){
			return 'test-string';
		});
		$filter = $this->filtry->make(array('attribute' => 'some data'), array('attribute' => 'custom_filter'));
		$data = $filter->getfiltered();
		$this->assertEquals('test-string', $data['attribute']);
	}

    public function testGetValuesWithoutFilters()
    {
        $filter = $this->filtry->make(array('attribute_1' => ' value ', 'attribute_2' => ' value '), array('attribute_1' => 'trim'));
        $data = $filter->getFiltered();
        $this->assertEquals(' value ', $data['attribute_2']);
    } 
}