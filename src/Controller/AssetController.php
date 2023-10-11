<?php


namespace App\Controller;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssetController extends AbstractController
{
    /**
     * @Route("/create-asset", name="create-asset")
     * @throws \Exception
     */
    public function createAssetAction(): Response
    {
        $assetPath = '/images/';
        $assetname = 'logo.png';

        // Create and save a new asset
        $newAsset = new Asset();
        $newAsset->setParentId(1);
        $newAsset->setFilename($assetname);
        $newAsset->setData(file_get_contents($_SERVER['DOCUMENT_ROOT']. '/images/logo.png'));
        $newAsset->setParent(Asset::getByPath($assetPath));

        // Save the employee
        $newAsset->save();

        return new JsonResponse(['message' => 'Asset created successfully']);
    }
}
