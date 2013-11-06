<?php namespace Filtry;

use Illuminate\Support\Facades\Facade;

class FiltryFacade extends Facade
{
	protected static function getFacadeAccessor() { return 'filtry'; }
}