<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

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
