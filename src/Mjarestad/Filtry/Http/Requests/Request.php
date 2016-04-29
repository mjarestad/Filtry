<?php namespace Mjarestad\Filtry\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    /**
     * Add filters from the filters method if it exists.
     */
    public function sanitize()
    {
        if (method_exists($this, 'filters')) {
            $filtry = \Filtry::make($this->all(), $this->filters());
            $this->merge($filtry->getFiltered());
        }
    }

    /**
     * Get the validator instance and execute the filter sanitation.
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->sanitize();

        return parent::getValidatorInstance();
    }
}

