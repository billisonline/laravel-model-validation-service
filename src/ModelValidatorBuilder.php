<?php

namespace BYanelli\SelfValidatingModels;

use BYanelli\Support\Validatorable;
use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationRuleParser;

//todo: use contract

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
    private $rulesets = [];

    /**
     * @var array
     */
    private $conditionalRulesets;

    /**
     * @var bool
     */
    private $creating;

    /**
     * @var bool
     */
    private $updating;

    /**
     * @var bool
     */
    private $deleting;

    /**
     * @param Model $model
     * @return $this
     * @throws Exception
     */
    public function setObject($model): Validatorable
    {
        if (!($model instanceof Model)) {
            throw new Exception;
        }

        $this->setModel($model);
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
            $rules = $this->mergeRules($rules, $ruleset);
        }

        /** @var Validator $validator */
        $validator = $this->validatorFactory->make($this->data, $rules);

        foreach ($this->conditionalRulesets as $item) {
            [$condition, $ruleset] = [$item['condition'], $item['ruleset']];

            foreach ($ruleset as $attribute => $rules) {
                $validator->sometimes($attribute, $rules, $condition);
            }
        }

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

    /**
     * @param bool|callable $condition
     * @param array $rules
     * @return $this
     */
    protected function when($condition, array $rules): self
    {
        $this->conditionalRulesets[] = [
            'condition' => $this->wrapCondition($condition),
            'ruleset'   => $rules,
        ];

        return $this;
    }

    private function wrapCondition($condition): callable
    {
        if (!is_callable($condition)) {
            return function () use ($condition): bool {
                return boolval($condition);
            };
        }

        return $condition;
    }

    private function setModel(Model $model): void
    {
        $this->model = $model;

        $camelCaseName = Str::camel(class_basename($model));

        $this->{$camelCaseName} = $model;

        $this->creating = !$this->model->exists;
        $this->updating = $this->model->exists;
        $this->deleting = false;
    }

    /**
     * @param bool $deleting
     * @return $this
     */
    public function setDeleting(bool $deleting): self
    {
        $this->deleting = $deleting;

        if ($deleting) {
            $this->creating = false;
            $this->updating = false;
        }

        return $this;
    }

    protected function saving(): bool
    {
        return ($this->creating || $this->updating);
    }

    //todo?
    /*protected function modifying(): bool
    {
        return ($this->updating || $this->deleting);
    }*/

    /**
     * @see \Illuminate\Validation\Validator::addRules()
     *
     * @param array $existing
     * @param array $new
     * @return array
     * @throws Exception
     */
    protected function mergeRules(array $existing, array $new): array
    {
        $response = (new ValidationRuleParser($this->data))->explode($new);

        if (!empty($response->implicitAttributes ?? [])) {
            throw new Exception('Implicit attributes not supported yet');
        }

        return array_merge_recursive($existing, $response->rules);
    }

    protected function updatingAttributes(...$attributes): bool
    {
        return $this->updating && $this->model->isDirty($attributes);
    }

    protected function updatingFrom(array $attributes)
    {
        if (!$this->updatingAttributes(...array_keys($attributes))) {
            return false;
        }

        foreach ($attributes as $name => $val) {
            if ($this->model->getOriginal($name) !== $val) {
                return false;
            }
        }

        return true;
    }

    protected function updatingTo(array $attributes)
    {
        if (!$this->updatingAttributes(...array_keys($attributes))) {
            return false;
        }

        foreach ($attributes as $name => $val) {
            if ($this->model->{$name} !== $val) {
                return false;
            }
        }

        return true;
    }

    protected function updatingFromTo(array $attributes): bool
    {
        foreach ($attributes as $name => [$from, $to]) {
            if (
                !$this->updatingFrom([$name => $from])
                || !$this->updatingTo([$name => $to])
            ) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCondition($condition, array $arguments): bool
    {
        return boolval(is_callable($condition)? $condition(...$arguments) : $condition);
    }

    private function whenStateAnd(bool $state, $andCondition, array $rules)
    {
        return $this->when(
            function () use ($state, $andCondition) {
                return (
                    $state
                    && $this->evaluateCondition($andCondition, func_get_args())
                );
            },
            $rules
        );
    }

    protected function whenSavingAnd($condition, array $rules)
    {
        return $this->whenStateAnd($this->saving(), $condition, $rules);
    }

    protected function whenCreatingAnd($condition, array $rules)
    {
        return $this->whenStateAnd($this->creating, $condition, $rules);
    }

    protected function whenUpdatingAnd($condition, array $rules)
    {
        return $this->whenStateAnd($this->updating, $condition, $rules);
    }

    protected function whenDeletingAnd($condition, array $rules)
    {
        return $this->whenStateAnd($this->deleting, $condition, $rules);
    }

    protected function whenSaving(array $rules)
    {
        return $this->whenSavingAnd(true, $rules);
    }

    protected function whenCreating(array $rules)
    {
        return $this->whenCreatingAnd(true, $rules);
    }

    protected function whenUpdating(array $rules)
    {
        return $this->whenUpdatingAnd(true, $rules);
    }

    protected function whenDeleting(array $rules)
    {
        return $this->whenDeletingAnd(true, $rules);
    }
}
