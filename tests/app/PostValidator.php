<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\ModelValidatorBuilder;

class PostValidator extends ModelValidatorBuilder
{
    /**
     * @var Post
     */
    protected $post;

    protected function build(): void
    {
        $this
            ->addRules([
                'title' => 'string|required|max:255',
            ])
            // Post may be created without body, but must include one when published
            ->addRulesWhenPublished([
                'body' => 'string|required|max:800',
            ]);
    }

    protected function addRulesWhenPublished(array $rules)
    {
        return $this->addRulesWhen($this->post->published, $rules);
    }
}
