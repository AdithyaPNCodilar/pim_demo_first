<?php

namespace MyBundle\Controller;

use Pimcore\Model\DataObject\Employee;
use Exception;
use Pimcore\Controller\FrontendController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmpController extends AbstractController
{
    /**
     * @throws Exception
     * @Route("/create-employee/{name}", name="custom_bundle_employee")
     */
    public function indexAction(Request $request, string $name): Response
    {
        $employee = new Employee();
        $employee->setName($name);

        return $this->render('MyBundle/employee/employee.html.twig', [
            'employee' => $employee,
        ]);
    }
}
