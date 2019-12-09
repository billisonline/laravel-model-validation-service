<?php

namespace BYanelli\SelfValidatingModels\Tests\TestApp;

use BYanelli\Support\Validatorable;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;

class CommentValidator implements Validatorable
{
    /**
     * @var array
     */
    private $data;

    private $object;

    /**
     * @var Factory
     */
    private $validatorFactory;

    public function __construct(Factory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    public function setData(array $data): Validatorable
    {
        $this->data = $data;

        return $this;
    }

    public function setObject($object): Validatorable
    {
        $this->object = $object;

        return $this;
    }

    public function toValidator(): Validator
    {
        return $this->validatorFactory->make($this->data, [
            'body' => 'string|required|max:255'
        ]);
    }
}
