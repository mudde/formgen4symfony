<?php

namespace Mudde\Formgen4Symfony\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearDbCommand extends Command
{
    protected static $defaultName = 'app:db:clear';
    protected EntityManagerInterface $entityManager;
    protected SymfonyStyle $io;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Drops and creates the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = $io = new SymfonyStyle($input, $output);

        try {
            $this->db_init();
            $this->schema_init();

            $this->entityManager->flush();

            $io->success('The default dataset is created!');
        } catch (\Exception $exception) {
            $io->error("ERROR! {$exception->getMessage()}");
        }
        return Command::SUCCESS;
    }

    protected function schema_init()
    {
        $application = $this->getApplication();
        $bitBucket = new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET);

        $this->io->text('☺ Schema create');

        $application
            ->find('doctrine:schema:create')
            ->run(new ArrayInput(['--quiet' => true, '--env' => 'dev']), $bitBucket);
    }

    protected function db_init()
    {
        $application = $this->getApplication();
        $bitBucket = new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET);

        $this->io->text('☺ Drop and create db');

        $application
            ->find('doctrine:database:drop')
            ->run(new ArrayInput(['--force' => true]), $bitBucket);

        $application
            ->find('doctrine:database:create')
            ->run(new ArrayInput([]), $bitBucket);

    }
}