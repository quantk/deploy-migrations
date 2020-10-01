<?php

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|Container
     * @throws BindingResolutionException
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed|Repository|string
     * @throws BindingResolutionException
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyUndefinedMethod
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }

        if (is_array($key)) {
            app('config')->set($key);

            return;
        }

        return app('config')->get($key) ?? $default;
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     * @throws BindingResolutionException
     * @psalm-suppress MixedMethodCall
     */
    function config_path($path = '')
    {
        /** @var string $make */
        $make = app()->make('path.config');
        return $make . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     * @return string
     * @throws BindingResolutionException
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyUndefinedMethod
     */
    function base_path($path = '')
    {
        return app()->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
