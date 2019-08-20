<?php


namespace App\Command;


use App\Entity\Quote;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $strJsonFileContents = file_get_contents("http://www.apilayer.net/api/live?access_key=48e7038235f8cb33c574f60f19fa5285&format=1");
        $project = json_decode($strJsonFileContents,true);
        $quot = $project["quotes"];
        foreach ($quot as $key => $value)
        {
            $quote = new Quote();
            $quote->setCurrency($key);
            $quote->setAmount($value);
            // tell Doctrine you want to (eventually) save the quote (no queries yet)
            $entityManager->persist($quote);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
        }
        $output->writeln("quotes added");
    }

}