<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\HasValidationRules;
use BYanelli\SelfValidatingModels\SelfValidates;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use SelfValidates, HasValidationRules;

    public $validationRules = [
        'email' => 'required|string|email',
    ];

    public $rules = [
        'email' => 'this_is_not_used|not_actual_validators'
    ];
}
