# Laravel Model Validation Service

![](https://travis-ci.org/billisonline/laravel-model-validation-service.svg?branch=master)

Auto-validating models for Laravel

## Basic usage

Write "validator builder" classes for your models, similar to Laravel's [authorization policies](https://laravel.com/docs/master/authorization). Validator builders either implement the `Validatorable` interface or extend the builtin `ModelValidatorBuilder` class, which allows you to add conditional validation rules for each state of your model, and for each point in its lifecycle.

For example, a `Post` model in a blog or CMS could have a corresponding `PostValidator` class:

```php
<?php

namespace App\Validators;

use BYanelli\SelfValidatingModels\ModelValidatorBuilder;
use Illuminate\Validation\Rule;

class PostValidator extends ModelValidatorBuilder
{
    /**
     * @var Post
     */
    protected $post;

    protected function build(): void
    {
        $this
            // Baseline rules on every create/update
            ->whenSaving([
                'title' => 'string|required|max:255',
            ])
            // Reason must be specified when unpublishing
            ->whenUnpublishing([
                'unpublish_reason' => 'string|required'
            ])
            // Protected posts cannot be deleted
            ->whenDeleting([
                'protected' => Rule::in(false)
            ]);
    }

    protected function whenUnpublishing(array $rules)
    {
        return $this->whenUpdatingAnd(
            $this->updatingFromTo(['published' => [true, false]]),
            $rules
        );
    }
}
```

Writing a model validator builder like the one above allows you to express conditional rules that would otherwise be difficult. For example, it requires any update that "unpublishes" a blog post to specify a reason why the post was unpublished.

Validator builders can be registered to observe model events using the `ModelValidationServiceProvider` with the following steps:

1. Create a provider class in your app/Providers folder that extends `ModelValidationServiceProvider`.
2. Add your provider to the list in `config/app.php`.
3. Register your validator using the `$validators` array, where the key is a model class name and the value is a validator builder class name. (This is similar to `$policies` in Laravel's `AuthorizationServiceProvider`: cf. the [Laravel documentation](https://laravel.com/docs/master/authorization#registering-policies).)

Your provider may look something like this:

```php
<?php

namespace App\Providers;

use App\Post;
use App\Validators\PostValidator;
use BYanelli\SelfValidatingModels\ModelValidationServiceProvider as BaseServiceProvider;

class ModelValidationServiceProvider extends BaseServiceProvider
{
    protected $validators = [
        Post::class => PostValidator::class,
    ];
}
```

## Advantages of model validators

Consider a blog post in the following state, with the above `PostValidator` observing it:

```php
$post = new Post([
    'title'             => 'Zeus caught in compromising position with local forest nymph',
    'body'              => '...',
    'published'         => true,
    'unpublish_reason'  => null,
]);

$post->save();
```

The following update would trigger a `ValidationException`:

```php
$post->published = false;

$post->save(); // throws ValidationException because unpublish_reason is missing
```

Whereas an update that included `unpublish_reason` would be valid:

```php
$post->published = false;
$post->unpublish_reason = 'angered Zeus, embarrassed Hera';

$post->save(); // succeeds
```

## How to test

`composer test` or `./vendor/bin/phpunit`
