<?php

namespace BYanelli\SelfValidatingModels;

use BYanelli\Support\Validatorable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory as ValidatorFactory;

class ModelValidationServiceProvider extends ServiceProvider
{
    protected $validators = [
        //
    ];

    public function register()
    {
        parent::register();

        $this->app->resolving(Validatorable::class, function (Validatorable $validatorable, Container $app) {
            $validatorable->setFactory($app->make(ValidatorFactory::class));
        });

        /** @var Model $modelClass */
        /** @var Validatorable $validatorBuilderClass */
        foreach ($this->validators as $modelClass => $validatorBuilderClass) {
            if (!is_subclass_of($modelClass, Model::class)) {
                throw new \Exception('zzz');
            }

            if (!is_subclass_of($validatorBuilderClass, Validatorable::class)) {
                throw new \Exception('zzz');
            }

            $modelClass::saving(function (Model $model) use ($validatorBuilderClass) {
                /** @var Validatorable $validatorBuilder */
                $validatorBuilder = $this->app->make($validatorBuilderClass);

                $validator = $validatorBuilder->setObject($model)->toValidator();

                $validator->validate();
            });

            $modelClass::deleting(function (Model $model) use ($validatorBuilderClass) {
                /** @var Validatorable $validatorBuilder */
                $validatorBuilder = $this->app->make($validatorBuilderClass);

                $validatorBuilder->setObject($model);

                if ($validatorBuilder instanceof ModelValidatorBuilder) {
                    $validatorBuilder->setDeleting(true);
                }

                $validatorBuilder->toValidator()->validate();
            });
        }
    }
}
