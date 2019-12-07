<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\HasValidationRules;
use BYanelli\SelfValidatingModels\SelfValidates;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use SelfValidates, HasValidationRules;

    public $rules = [
        'title' => 'required|string|max:20',
    ];
}
