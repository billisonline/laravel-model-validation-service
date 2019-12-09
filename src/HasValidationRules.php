<?php

namespace BYanelli\SelfValidatingModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Validation\Factory;

/**
 * @mixin Model
 */
trait HasValidationRules
{
    public function validator()
    {
        /** @var Factory $validatorFactory */
        $validatorFactory = app(Factory::class);

        return $validatorFactory->make($this->toArray(), $this->rules);
    }
}
