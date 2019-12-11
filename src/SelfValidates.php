<?php

namespace BYanelli\SelfValidatingModels;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SelfValidates
{
    public static function bootSelfValidates()
    {
        static::saving(function (Model $model) {
            $validator = app()->call([$model, 'validator']);

            if (!($validator instanceof Validator)) {
                throw new \Exception;
            }

            $validator->validate();
        });
    }
}
