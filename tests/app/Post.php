<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string title
 * @property string body
 * @property bool published
 * @property string unpublish_reason
 * @property bool protected
 */
class Post extends Model
{
    protected $casts = [
        'published' => 'bool',
        'protected' => 'bool',
    ];
}
