<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Misc\VersionManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;

#[AsCommand(
    name: 'app:version',
    description: 'Displays the current app version.',
)]
class VersionCommand extends Command
{
    public function __construct(
        protected readonly VersionManager $versionManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Version Information');

        $appVersion = $this->versionManager->getVersion();
        $newVersion = $this->versionManager->getVersionFromProvider()->withBuild($appVersion->getBuild());
        $hasUpdate = $this->versionManager->hasUpdate();

        $version = $hasUpdate ?
            '<error>' . $appVersion->toString() . '</error> => <info>' . $newVersion->toString() . '</info>' :
            $appVersion->toString();

        $table = new Table($output);
        $table
            ->setHeaders(['App Version', 'PHP Version', 'Symfony Version'])
            ->setRows([
                [
                    $version,
                    PHP_VERSION,
                    $this->getApplication()?->getVersion()
                ],
            ])
            ->setColumnWidth(1, 5)
            ->setVertical()
            ->setStyle('compact')
            ->render()
        ;

        $io->newLine();
        if ($hasUpdate) {
            // your app might run in an older version
            $io->warning(['A new version is available. Please run the cache:clear command.']);
        }

        return Command::SUCCESS;
    }
}
