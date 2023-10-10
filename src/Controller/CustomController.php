<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Department;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomController extends FrontendController
{
    public function customAction(Request $request): Response
    {
        $custom = Department::getById(4);
        return $this->render('employee/employee.html.twig', [
            'custom' => $custom,
        ]);
    }
}

