<?php

namespace BYanelli\SelfValidatingModels;

use BYanelli\Support\Validatorable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Factory as ValidatorFactory; //todo: use contract

abstract class ModelValidatorBuilder implements Validatorable
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    private $rulesets;

    /**
     * @param Model $model
     * @return $this
     * @throws \Exception
     */
    public function setObject($model): Validatorable
    {
        if (!($model instanceof Model)) {
            throw new \Exception;
        }

        $this->model = $model;

        $this->setData($model->toArray());

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data): Validatorable
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param ValidatorFactory $factory
     * @return $this
     */
    public function setFactory(ValidatorFactory $factory): Validatorable
    {
        $this->validatorFactory = $factory;

        return $this;
    }

    public function toValidator(): Validator
    {
        $this->build();

        $rules = [];

        foreach ($this->rulesets as $ruleset) {
            $rules = array_merge($rules, $ruleset);
        }

        /** @var Validator $validator */
        $validator = $this->validatorFactory->make($this->data, $rules);

        /*foreach ($this->conditionalRulesets as $item) {
            [$condition, $ruleset] = [$item['condition'], $item['ruleset']];

            foreach ($ruleset as $attribute => $rules) {
                $validator->sometimes($attribute, $rules, $condition);
            }
        }*/

        return $validator;
    }

    abstract protected function build(): void;

    /**
     * @param array $rules
     * @return $this
     */
    protected function addRules(array $rules): self
    {
        $this->rulesets[] = $rules;

        return $this;
    }
}
