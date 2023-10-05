<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class   InheritanceController extends FrontendController
{
    #[Template('employee/inheritance.html.twig')]
    public function defaultAction(Request $request): array
    {
        return [];
    }

}
