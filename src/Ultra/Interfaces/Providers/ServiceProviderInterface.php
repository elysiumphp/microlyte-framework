<?php

declare(strict_types=1);

namespace Ultra\Interfaces;

use DI\Container;

interface ServiceProviderInterface
{

    /**
     * Service register name
     */
    public function name(): string;

    /**
     * Register new service on dependency container
     *
     * @param Container $container
     * @return mixed
     */
    public function register(Container $container);
}