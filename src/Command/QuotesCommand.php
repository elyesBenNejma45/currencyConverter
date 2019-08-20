<?php


namespace App\Command;


use App\Entity\Quote;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use mysql_xdevapi\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
Use OceanApplications\currencylayer;


class QuotesCommand extends Command
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-quote';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Creates a new quote.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->container->get('doctrine')->getManager();
        /** @var EntityRepository $repository */
        $repository = $this->container->get('doctrine')->getRepository(Quote::class);
        $quotes = $repository->findAll();
        foreach ($quotes as $quote) {
            $entityManager->remove($quote);
            $entityManager->flush();
        }
        $output->writeln("quotes deleted");
        $currencyLayer = new currencylayer\client('48e7038235f8cb33c574f60f19fa5285');
        $result = $currencyLayer
            ->source('USD')
            ->currencies('CHF,EUR')
            ->amount('1')
            ->live();

        $euroToUsd = 1/$result['quotes']['USDEUR'];
        $quoteUsd = new Quote();
        $quoteUsd->setCurrency('USD');
        $quoteUsd->setAmount($euroToUsd);
        $entityManager->persist($quoteUsd);
        $entityManager->flush();

        $euroToChf = $euroToUsd * $result['quotes']['USDCHF'];
        $quoteChf = new Quote();
        $quoteChf->setCurrency('CHF');
        $quoteChf->setAmount($euroToChf);
        $entityManager->persist($quoteChf);
        $entityManager->flush();
        $output->writeln("quotes Created");
    }
}