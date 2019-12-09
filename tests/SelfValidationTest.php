<?php

namespace BYanelli\SelfValidatingModels\Tests;

use BYanelli\SelfValidatingModels\Tests\TestApp\Comment;
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

    public function testSelfValidatesWithValidatorAndRules()
    {
        $this->expectException(ValidationException::class);

        $comment = new Comment;

        $comment->body = Str::random(500);

        $comment->save();
    }
}
