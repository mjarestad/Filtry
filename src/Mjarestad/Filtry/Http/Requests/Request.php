<?php namespace Mjarestad\Filtry\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function sanitize()
    {
        if (method_exists($this, 'filters')) {
            $filtry = \Filtry::make($this->all(), $this->filters());
            $this->merge($filtry->getFiltered());
        }
    }

    protected function getValidatorInstance()
    {
        $this->sanitize();

        return parent::getValidatorInstance();
    }
}

