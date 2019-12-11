<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\SelfValidatesWithValidator;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use SelfValidatesWithValidator;

    protected $validator = CommentValidator::class;
}
