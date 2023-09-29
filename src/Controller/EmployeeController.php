<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends FrontendController
{
    #[Template('employee/home.html.twig')]
    public function defaultAction(Request $request): array
    {
        return [];
    }
    public function signIn(Request $request): Response
    {
        return $this->render('employee/signin.html.twig');
    }

    #[Template('employee/my-gallery.html.twig')]
    public function myGalleryAction(Request $request): array
    {
        if ('asset' === $request->get('type')) {
            $asset = Asset::getById((int) $request->get('id'));
            if ('folder' === $asset->getType()) {
                return [
                    'assets' => $asset->getChildren()
                ];
            }
        }

        return [];
    }

    #[Template('employee/footer.html.twig')]
    public function footerAction(Request $request): Response
    {
        return $this->render('employee/footer.html.twig');
    }
}
