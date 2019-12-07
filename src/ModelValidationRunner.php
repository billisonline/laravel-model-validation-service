<?php

namespace BYanelli\SelfValidatingModels;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Factory;

class ModelValidationRunner
{
    /**
     * @var Factory
     */
    private $validatorFactory;

    /**
     * @var Model
     */
    private $model;

    public function __construct(Factory $validationFactory)
    {
        $this->validatorFactory = $validationFactory;
    }

    public function validate(Model $model)
    {
        $this->model = $model;

        // todo
        /*if ($this->hasExternalValidator()) {
            return $this->runExternalValidator();
        }*/

        return $this->runInternalValidator();
    }

    private function runInternalValidator(): bool
    {
        $this->internalValidator()->validate();

        return true;
    }

    private function internalValidator(): Validator
    {
        return app()->call([$this->model, 'validator']);
    }
}
