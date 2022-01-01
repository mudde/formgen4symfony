<?php

namespace Mudde\Formgen4Symfony\Api;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: AbstractActiveApiController::entiy)]
abstract class AbstractActiveApiController
{
    const entiy = 'asdf';
}