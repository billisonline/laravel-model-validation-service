<?php

namespace BYanelli\SelfValidatingModels;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SelfValidates
{
    public static function bootSelfValidates()
    {
        static::saving(function (Model $model) {
            $runner = app(ModelValidationRunner::class);

            $runner->validate($model);
        });
    }
}
