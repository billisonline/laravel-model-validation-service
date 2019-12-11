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
        $messages = $this->validationMessages ?? $this->messages ?? [];

        return $validatorFactory->make($data, $rules, $messages);
    }
}
