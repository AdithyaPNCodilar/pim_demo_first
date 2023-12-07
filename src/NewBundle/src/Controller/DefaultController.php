<?php

namespace NewBundle\Controller;

use Exception;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/new")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from new');
    }
    /**
     * @throws Exception
     * @Route("/create-employee/{name}", name="custom_bundle_employee")
     */
    public function createEmpAction(Request $request, string $name): Response
    {
        $employee = new Employee();
        $employee->setName($name);

        return $this->render('@NewBundle/employee/employee.html.twig', [
            'employee' => $employee,
        ]);
    }
}
