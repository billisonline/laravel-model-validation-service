<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\ModelValidationServiceProvider as BaseServiceProvider;

class ModelValidationServiceProvider extends BaseServiceProvider
{
    protected $validators = [
        Post::class => PostValidator::class,
    ];
}
