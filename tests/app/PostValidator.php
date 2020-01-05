<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\ModelValidatorBuilder;

class PostValidator extends ModelValidatorBuilder
{
    protected function build(): void
    {
        $this
            ->addRules([
                'title' => 'string|required|max:255'
            ]);
    }
}
