<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string title
 * @property bool published
 */
class Post extends Model
{
    protected $casts = [
        'published' => 'bool'
    ];
}
