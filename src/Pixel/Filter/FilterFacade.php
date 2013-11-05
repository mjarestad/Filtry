<?php namespace Pixel\Filter;

use Illuminate\Support\Facades\Facade;

class FilterFacade extends Facade
{
	protected static function getFacadeAccessor() { return 'filter'; }
}