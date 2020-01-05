<?php

namespace BYanelli\SelfValidatingModels\Tests\Support;

use Throwable;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
trait ExpectsCustomExceptions
{
    protected $expectedCustomExceptionCallback = null;

    /**
     * @before
     */
    public function clearExpectedCustomExceptionCallback()
    {
        $this->expectedCustomExceptionCallback = null;
    }

    protected function expectCustomException(callable $callback)
    {
        $this->expectedCustomExceptionCallback = $callback;
    }

    /**
     * @param Throwable $t
     * @throws Throwable
     */
    protected function onNotSuccessfulTest(Throwable $t)
    {
        /** @var callable $callback */
        if (is_callable($callback = $this->expectedCustomExceptionCallback)) {
            $callback($t);

            return;
        }

        throw $t;
    }
}
