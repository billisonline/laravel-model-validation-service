<?php

namespace BYanelli\SelfValidatingModels\Tests\Support;

use BYanelli\SelfValidatingModels\Tests\Support\ExpectsCustomExceptions;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * @mixin TestCase
 * @mixin ExpectsCustomExceptions
 */
trait ExpectsValidationExceptions
{
    /**
     * @var ValidationException
     */
    protected $currentValidationException;

    protected function expectValidationException(callable $callback)
    {
        $this->expectCustomException(function (ValidationException $e) use (&$callback) {
            $this->currentValidationException = $e;

            try {
                $callback();
            } catch (\Throwable $t) {
                echo "Current validation errors: ".json_encode($e->errors());

                throw $t;
            }
        });

    }

    protected function assertValidationError(string $attribute, $rules=[])
    {
        $rules = Arr::wrap($rules);

        $errors = $this->currentValidationException->errors();

        $this->assertContains($attribute, array_keys($errors));

        foreach ($rules as $rule) {
            $this->assertContains($rule, $errors[$attribute]);
        }
    }

    protected function expectValidationErrors(array $errors)
    {
        $this->expectValidationException(function () use ($errors) {
            foreach ($errors as $attribute => $rules) {
                $this->assertValidationError($attribute, $rules);
            }
        });
    }
}
