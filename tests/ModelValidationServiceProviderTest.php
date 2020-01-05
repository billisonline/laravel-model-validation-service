<?php

namespace BYanelli\SelfValidatingModels\Tests;

use BYanelli\SelfValidatingModels\Tests\TestApp\Post;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ModelValidationServiceProviderTest extends TestCase
{
    public function testSelfValidatesWithServiceProvider()
    {
        $this->expectException(ValidationException::class);

        $post = new Post();

        $post->title = Str::random(500);

        $post->save();
    }
}
