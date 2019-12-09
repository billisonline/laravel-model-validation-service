<?php

namespace BYanelli\SelfValidatingModels;

use BYanelli\Support\Validatorable;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasValidator
{
    public function validator()
    {
        if (!is_subclass_of($validatorClass = $this->validator, Validatorable::class)) {
            throw new \Exception('Validator class must implement Validatorable interface');
        }

        /** @var Validatorable $validatorBuilder */
        $validatorBuilder = app($validatorClass);

        return (
            $validatorBuilder
                ->setData($this->toArray())
                ->setObject($this)
                ->toValidator()
        );
    }
}
