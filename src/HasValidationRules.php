<?php

namespace BYanelli\SelfValidatingModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Factory;

/**
 * @mixin Model
 */
trait HasValidationRules
{
    public function validator(Factory $validatorFactory)
    {
        $data = $this->toArray();
        $rules = $this->validationRules ?? $this->rules;

        return $validatorFactory->make($data, $rules);
    }
}
