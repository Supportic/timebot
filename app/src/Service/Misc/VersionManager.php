<?php

declare(strict_types=1);

namespace App\Service\Misc;

use RuntimeException;
use Shivas\VersioningBundle\Service\VersionManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Version\Version;

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

    /**
     * Compares cached version getVersion() with the newest version getVersionFromProvider().
     * @throws RuntimeException
     */
    public function hasUpdate(): bool
    {
        $currentVersion = $this->versionManager->getVersion();
        $newVersion = $this->versionManager->getVersionFromProvider();
        //  does not compare build version
        // return $newVersion->isNotEqualTo($currentVersion);
        // Compare only major.minor.patch, ignoring build hash
        if ($newVersion->getMajor() !== $currentVersion->getMajor()) {
            return true;
        }

        if ($newVersion->getMinor() !== $currentVersion->getMinor()) {
            return true;
        }

        return $newVersion->getPatch() !== $currentVersion->getPatch();
    }

    /**
     * Always newest.
     * Retrieve the current not cached version of the application from the VERSION file.
     * @throws RuntimeException
     */
    public function getVersionFromProvider(): Version
    {
        return $this->versionManager->getVersionFromProvider();
    }

    /**
     * Retrieve the current cached version of the application with commit hash.
     * @throws RuntimeException
     */
    public function getVersion(): Version
    {
        return $this->versionManager->getVersion()->withBuild($this->getGitCommitHash());
    }

    /**
     * Retrieve the current cached version of the application.
     * @throws RuntimeException
     */
    public function getVersionShort(): Version
    {
        return $this->versionManager->getVersion();
    }

    public function incrementMajorVersion(): void
    {
        $currentVersion = $this->versionManager->getVersion();

        $this->writeVersion($currentVersion->incrementMajor());
    }

    public function incrementMinorVersion(): void
    {
        $currentVersion = $this->versionManager->getVersion();

        $this->writeVersion($currentVersion->incrementMinor());
    }

    public function incrementPatchVersion(): void
    {
        $currentVersion = $this->versionManager->getVersion();

        $this->writeVersion($currentVersion->incrementPatch());
    }

    private function writeVersion(Version $version): void
    {
        $this->versionManager->writeVersion($version->withBuild($this->getGitCommitHash()));
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
        // remote
        $filePath = $this->projectDir . DIRECTORY_SEPARATOR . '.git/refs/remotes/origin/' . $this->getGitBranchName();
        if (!is_file($filePath)) {
            // local
            $filePath = $this->projectDir . DIRECTORY_SEPARATOR . '.git/refs/heads/' . $this->getGitBranchName();
        }

        if (is_file($filePath)) {
            $head = file($filePath);

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
        if (is_file($this->projectDir . DIRECTORY_SEPARATOR . '.git/HEAD')) {
            $git = file($this->projectDir . '/.git/HEAD');

            if ($git === false || !isset($git[0])) {
                return null;
            }

            $head = explode('/', $git[0], 3);

            if (!isset($head[2])) {
                return null;
            }

            return trim($head[2]);
        }

        return null; // this is not a Git installation
    }
}
