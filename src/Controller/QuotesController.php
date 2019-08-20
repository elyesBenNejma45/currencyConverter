<?php

namespace App\Controller;

use App\Entity\Quote;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuotesController extends AbstractController
{
    /**
     * @Route("/quotes", name="quotes")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/QuotesController.php',
        ]);
    }

    /**
     * @Route("/Quotes", name="create_quotes")
     */
    public function displayQuote()
    {
        /** @var EntityRepository $repository */
        $repository = $this->container->get('doctrine')->getRepository(Quote::class);
        $quotes = $repository->findAll();
        return $this->render('quote.html.twig', ['quotes' => $quotes]);
    }
}
