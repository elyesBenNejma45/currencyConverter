<?php

namespace App\Controller;

use App\Entity\Quote;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @Route("/Quotes", name="convert_quotes")
     */
    public function convertQuote(RequestStack $requestStack)
    {

        $request = $requestStack->getCurrentRequest();
        $euroAmont = $request->get('amount');
        $convertedAmount = 0;
        if (!empty($euroAmont)){
            /** @var EntityRepository $repository */
            $repository = $this->container->get('doctrine')->getRepository(Quote::class);
            /** @var Quote $quote */
            $quote = $repository->findOneBy(array('currency' => $request->get('currency')));
            $convertedAmount = $euroAmont * $quote->getAmount();
        }
        return $this->render('quote.html.twig', ['quote' => $convertedAmount, 'amount' => $euroAmont, 'currency' => $request->get('currency')]);
    }
}
