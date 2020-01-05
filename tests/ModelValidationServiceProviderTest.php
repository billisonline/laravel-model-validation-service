<?php

namespace BYanelli\SelfValidatingModels\Tests;

use BYanelli\SelfValidatingModels\Tests\TestApp\Post;
use Illuminate\Support\Str;

class ModelValidationServiceProviderTest extends TestCase
{
    public function testValidatesWithServiceProvider()
    {
        $this->expectValidationErrors(['title' => 'validation.max.string']);

        $post = new Post();
        $post->title = Str::random(500);

        $post->save();
    }

    public function testValidatesConditionalRules()
    {
        $this->expectValidationErrors(['body' => 'validation.required']);

        $post = new Post();
        $post->title = Str::random(100);
        $post->published = 1;

        $post->save();
    }
}
