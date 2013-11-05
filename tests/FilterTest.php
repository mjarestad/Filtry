<?php

use Pixel\Filter\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
{
	public $filter;

	public function setUp()
	{
		$this->filter = new Filter;
	}

	public function testTrim()
	{
		$filtered = $this->filter->trim(' test ');
		$this->assertEquals('test', $filtered);
	}

	public function testLtrim()
	{
		$filtered = $this->filter->ltrim(' test');
		$this->assertEquals('test', $filtered);
	}

	public function testRtrim()
	{
		$filtered = $this->filter->rtrim('test ');
		$this->assertEquals('test', $filtered);
	}

	public function testStrToLower()
	{
		$filtered = $this->filter->lower('TEST');
		$this->assertEquals('test', $filtered);
	}

	public function testStrToUpper()
	{
		$filtered = $this->filter->upper('test');
		$this->assertEquals('TEST', $filtered);
	}

	public function testUcFirst()
	{
		$filtered = $this->filter->ucfirst('test');
		$this->assertEquals('Test', $filtered);
	}

	public function testUcWords()
	{
		$filtered = $this->filter->ucwords('test some more');
		$this->assertEquals('Test Some More', $filtered);
	}

	public function testStripSlashes()
	{
		$filtered = $this->filter->stripslashes('\test');
		$this->assertEquals('test', $filtered);
	}

	public function testXssClean()
	{
		$filtered = $this->filter->xssClean('<script>eval("test");</script>');
		$this->assertEquals('&lt;script&gt;eval(&quot;test&quot;);&lt;/script&gt;', $filtered);
	}

	public function testRemoveWhitespace()
	{
		$filtered = $this->filter->removeWhitespace('test some more');
		$this->assertEquals('testsomemore', $filtered);
	}

	public function testSlug()
	{
		$filtered = $this->filter->slug('test Some MORE');
		$this->assertEquals('test-some-more', $filtered);
	}

	public function testPrepUrl()
	{
		$filtered = $this->filter->prepUrl('www.test.com');
		$this->assertEquals('http://www.test.com', $filtered);
	}

	public function testCamelCase()
	{
		$filtered = $this->filter->camelCase('test_camel_case');
		$this->assertEquals('testCamelCase', $filtered);
	}

	public function testSnakeCase()
	{
		$filtered = $this->filter->snakeCase('testSnakeCase');
		$this->assertEquals('test_snake_case', $filtered);
	}

	public function testStudlyCase()
	{
		$filtered = $this->filter->studlyCase('test_studly_case');
		$this->assertEquals('TestStudlyCase', $filtered);
	}

	public function testGetOld()
	{
		$filter = $this->filter->make(array('attribute' => ' test get filtered'), array('attribute' => 'trim|ucwords'));
		$filtered = $filter->getOld();
		$this->assertEquals(' test get filtered', $filtered['attribute']);
	}

	public function testgetFiltered()
	{
		$filter = $this->filter->make(array('attribute' => ' test get filtered'), array('attribute' => 'trim|ucwords'));
		$filtered = $filter->getFiltered();
		$this->assertEquals('Test Get Filtered', $filtered['attribute']);
	}

	public function testCreateCustomFilter()
	{
		$this->filter->extend('custom_filter', function(){
			return 'test-string';
		});
		$filtered = $this->filter->customFilter('some data');
		$this->assertEquals('test-string', $filtered);
	}

	public function testMakeWithCustomFilter()
	{
		$this->filter->extend('custom_filter', function(){
			return 'test-string';
		});
		$filter = $this->filter->make(array('attribute' => 'some data'), array('attribute' => 'custom_filter'));
		$filtered = $filter->getFiltered();
		$this->assertEquals('test-string', $filtered['attribute']);
	}
}