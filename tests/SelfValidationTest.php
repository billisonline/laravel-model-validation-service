<?php

namespace BYanelli\SelfValidatingModels\Tests;

use BYanelli\SelfValidatingModels\Tests\TestApp\Post;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SelfValidationTest extends TestCase
{
    public function testSelfValidatesWithTraitAndRules()
    {
        $this->expectException(ValidationException::class);

        $post = new Post;

        $post->title = Str::random(30);

        $post->save();
    }

    public function testValidModelAllowedWithTraitAndRules()
    {
        $post = new Post;

        $post->title = Str::random(20);

        $post->save();

        $this->assertTrue($post->exists);
    }
}
