# Laravel Self-Validating Models

![](https://travis-ci.org/billisonline/laravel-self-validating-models.svg?branch=master)

Self-validating model classes for Laravel

## How to use

Self-validate using an array of rules by adding the `SelfValidatesWithRules` trait and a `$rules` array to the class. An optional `$messages` array can be provided to supply custom messages.

```php
<?php

use BYanelli\SelfValidatingModels\SelfValidatesWithRules;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use SelfValidatesWithRules;

    public $rules = [
        'title' => 'required|string|max:20',
    ];

    public $messages = [
        'max' => 'The :attribute is too long bro',
    ];
}
```

Self-validate using a custom validator builder by adding the `SelfValidatesWithBuilder` trait and specifying the `$validator` property, which should point to a class that implements the `Validatorable` interface.

Comment.php:
```php
<?php

use BYanelli\SelfValidatingModels\SelfValidatesWithBuilder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use SelfValidatesWithBuilder;

    protected $validator = CommentValidator::class;
}
```

CommentValidator.php:
```php
<?php

use BYanelli\Support\Validatorable;
use Illuminate\Contracts\Validation\Validator;

class CommentValidator implements Validatorable
{
    //...implement setData() and setObject()

    public function toValidator(): Validator
    {
        return $this->validatorFactory->make($this->data, [
            'body' => 'string|required|max:255'
        ]);
    }
}
```

## How to test

`composer test` or `./vendor/bin/phpunit`
