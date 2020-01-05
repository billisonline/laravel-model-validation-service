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

    public function testValidationFailsWhenConditionalRulesViolated()
    {
        $this->expectValidationErrors(['body' => 'validation.required']);

        $post = new Post();
        $post->title = Str::random(100);
        $post->published = true;

        $post->save();
    }

    public function testValidationSucceedsWhenConditionalRulesFollowed()
    {
        $post = new Post();
        $post->title = Str::random(100);
        $post->published = true;
        $post->body = Str::random(100);

        $post->save();

        $this->assertTrue($post->exists);
    }

    public function testValidationFailsWhenUpdatingRulesViolated()
    {
        $this->expectValidationErrors(['unpublish_reason' => 'validation.required']);

        $post = new Post();
        $post->title = Str::random(100);
        $post->body = Str::random(100);
        $post->published = true;

        $post->save();

        $this->assertTrue($post->exists);

        $post->published = false;

        $post->save();
    }

    public function testValidationSucceedsWhenUpdatingRulesFollowed()
    {
        $post = new Post();
        $post->title = Str::random(100);
        $post->body = Str::random(100);
        $post->published = true;

        $post->save();

        $this->assertTrue($post->exists);

        $post->published = false;
        $post->unpublish_reason = 'zzz';

        $post->save();

        $this->assertTrue($post->exists);
    }
}
