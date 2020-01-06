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
            // Baseline rules on every create/update
            ->whenSaving([
                'title' => 'string|required|max:255',
            ])
            // Unpublish reason cannot be set yet when creating
            ->whenCreating([
                'unpublish_reason' => Rule::in(null, '')
            ])
            // Post may be created without body, but must include one when published
            ->whenPublished([
                'body' => 'string|required|max:800',
            ])
            // Reason must be specified when unpublishing
            ->whenUnpublishing([
                'unpublish_reason' => 'string|required'
            ])
            // Protected posts cannot be deleted
            ->whenDeleting([
                'protected' => Rule::in(false)
            ]);
    }

    protected function whenPublished(array $rules)
    {
        return $this->whenSavingAnd($this->post->published, $rules);
    }

    protected function whenUnpublishing(array $rules)
    {
        return $this->whenUpdatingAnd(
            $this->updatingFromTo(['published' => [true, false]]),
            $rules
        );
    }
}
