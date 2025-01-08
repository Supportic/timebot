<?php

declare(strict_types=1);

namespace App\Service\Misc;

use RuntimeException;
use Shivas\VersioningBundle\Service\VersionManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class VersionManager
{
    public function __construct(
        protected readonly VersionManagerInterface $versionManager,
        #[Autowire(param: 'kernel.project_dir')] protected readonly string $projectDir
    ) {}

    public function getManager(): VersionManagerInterface
    {
        return $this->versionManager;
    }

    public function hasUpdate(): bool
    {
        return $this->versionManager
            ->getVersionFromProvider()
            ->isNotEqualTo($this->versionManager->getVersion());
    }

    /**
     * Retrieve the current version of the application.
     * @return string
     * @throws RuntimeException
     */
    public function getVersion(): string
    {
        $version = $this->versionManager->getVersion()->toString();

        // if ($this->getGitBranchName() !== null) {
        //     $version .= ' Git branch: ' . $this->getGitBranchName();
        //     $version .= ', Git commit: ' . $this->getGitCommitHash();
        // }

        return $version;
    }

    public function getVersionFromProvider(): string
    {
        return $this->versionManager->getVersionFromProvider()->toString();
    }

    /**
     * Get hash of the last git commit (on remote "origin"!).
     *
     * If this method does not work, try to make a "git pull" first!
     *
     * @param int $length if this is smaller than 40, only the first $length characters will be returned
     *
     * @return string|null The hash of the last commit, null If this is no Git installation
     */
    private function getGitCommitHash(int $length = 7): ?string
    {
        $filename = $this->projectDir . '/.git/refs/remotes/origin/' . $this->getGitBranchName();
        if (is_file($filename)) {
            $head = file($filename);

            if (!isset($head[0])) {
                return null;
            }

            $hash = $head[0];

            return substr($hash, 0, $length);
        }

        return null; // this is not a Git installation
    }

    /**
     * Get the Git branch name of the installed system.
     *
     * @return string|null The current git branch name. Null, if this is no Git installation
     */
    private function getGitBranchName(): ?string
    {
        if (is_file($this->projectDir . '/.git/HEAD')) {
            $git = file($this->projectDir . '/.git/HEAD');
            $head = explode('/', $git[0], 3);

            if (!isset($head[2])) {
                return null;
            }

            return trim($head[2]);
        }

        return null; // this is not a Git installation
    }
}
