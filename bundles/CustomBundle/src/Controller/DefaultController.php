<?php

namespace CustomBundle\Controller;

use Pimcore\Model\DataObject\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/custom")
     */
    public function indexAction(Request $request): Response
    {
        return new Response('Hello world from Product custom Bundle..');
    }
    /**
     * @Route("/product/{id}")
     */
    public function productAction(Request $request, int $id): Response
    {
        $product = Product::getById($id);

        return $this->render('@CustomBundle/product/product.html.twig', [
            'product'=>$product,
        ]);
    }
}
