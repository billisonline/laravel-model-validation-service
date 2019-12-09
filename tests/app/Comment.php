<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\HasValidator;
use BYanelli\SelfValidatingModels\SelfValidates;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use SelfValidates, HasValidator;

    protected $validator = CommentValidator::class;
}
