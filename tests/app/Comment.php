<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\SelfValidatesWithBuilder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use SelfValidatesWithBuilder;

    protected $validator = CommentValidator::class;
}
