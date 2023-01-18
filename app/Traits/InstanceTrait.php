<?php

namespace App\Traits;

trait InstanceTrait
{
    /**
     * The current globally available container (if any).
     * @var static
     */
    protected static self $instance;

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}
