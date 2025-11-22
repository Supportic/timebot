<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Personio\Api\v1\ApiAttendanceService;
use App\Service\Personio\Api\v1\ApiEmployeeService;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

#[AsCommand(
    name: 'app:personio:sync',
    description: 'Pulls data from personio, parses it and saves it into the DB.',
)]
class PersonioSyncCommand extends Command
{
    public function __construct(
        private readonly ApiEmployeeService $apiEmployeeService,
        private readonly ApiAttendanceService $apiAttendanceService,
        private readonly Stopwatch $stopwatch,
        private readonly LoggerInterface $performanceLogger,
        #[Autowire(param: 'app.personio.api.v1.base_uri')]
        private readonly string $personioApiBaseUri,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->stopwatch->openSection();

        $this->stopwatch->start('employees', 'personio.api.fetch');

        $employees = $this->apiEmployeeService->fetchEmployees();
        // $evt = $this->stopwatch->stop('employees', 'personio.api.fetch');

        // $this->performanceLogger->info($evt->__toString());
        // $this->performanceLogger->info($evt->getCategory() . ' - ' . $evt->getDuration());
        $startDate = (new DateTime('today'))->setDate(2023, 1, 1);
        $endDate = (new DateTime())->setDate(2023, 12, 31)->setTime(23, 59, 59);

        $this->stopwatch->start('attendances', 'personio.api.fetch');
        $attendances = $this->apiAttendanceService->fetchAttendances($startDate, $endDate);

        $this->stopwatch->stopSection('fetching');

        $events = $this->stopwatch->getSectionEvents('fetching');

        /** @var StopwatchEvent $event */
        foreach ($events as $event) {
            if ('__section__' === $event->getName()) {
                continue;
            }

            $this->performanceLogger->info('{category}/{name} - {duration} ms', [
                'category' => $event->getCategory(),
                'name' => $event->getName(),
                'duration' => (int) $event->getDuration(),
                'location' => self::class . ':' . __LINE__
            ]);
        }

        // $this->performanceLogger->info(var_export($events,true));


        // foreach ($employees as $key => $employee) {
        //     $name = $employee->getAttributes()->getFirstName()->getValue();
        //     echo $name.PHP_EOL;
        // }

        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
