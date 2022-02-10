<?php

namespace Mudde\Formgen4Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MuddeFormgen4symfonyBundle extends Bundle
{
    public function boot():void
    {
    }

    public function build(ContainerBuilder $container):void {
    }

    public function shutdown():void
    {
    }
}