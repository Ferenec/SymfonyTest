<?php
namespace App\Controller;

use App\Entity\ProductData;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{

    /**
     * @Route("/")
     */
    public function index()
    {
        $products = $this->getDoctrine()
            ->getRepository(ProductData::class)
            ->findAll();

        return $this->render('main.html.php', [
            'products' => $products
        ]);
    }

}