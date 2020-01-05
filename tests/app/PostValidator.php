<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\SelfValidatingModels\ModelValidatorBuilder;
use Illuminate\Validation\Rule;

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
            ])
            // Reason must be specified when unpublishing
            ->addRulesWhenUnpublishing([
                'unpublish_reason' => 'string|required'
            ])
            // Protected posts cannot be deleted
            ->addRulesWhenDeleting([
                'protected' => Rule::in(false)
            ]);
    }

    protected function addRulesWhenPublished(array $rules)
    {
        return $this->addRulesWhen($this->post->published, $rules);
    }

    protected function addRulesWhenUnpublishing(array $rules)
    {
        return $this->addRulesWhen($this->updatingFromTo(['published' => [true, false]]), $rules);
    }
}
