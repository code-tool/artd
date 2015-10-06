<?php

namespace CodeTool\ArtifactDownloader;

use CodeTool\ArtifactDownloader\CmdRunner\CmdRunnerInterface;
use Seld\PharUtils\Timestamps;
use Symfony\Component\Finder\Finder;

class Compiler
{
    const DEFAULT_PHAR_FILE = 'artifact-downloader.phar';

    /**
     * @var CmdRunnerInterface
     */
    private $cmdRunner;

    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var string|null
     */
    private $version;

    /**
     * @var \DateTime|null
     */
    private $versionDate;

    /**
     * @param CmdRunnerInterface $cmdRunner
     * @param string             $baseDir
     */
    public function __construct(CmdRunnerInterface $cmdRunner, $baseDir = __DIR__)
    {
        $this->cmdRunner = $cmdRunner;
        $this->baseDir = $baseDir;
    }

    protected function getBaseDir()
    {
        return $this->baseDir;
    }

    protected function getRootDir()
    {
        return $this->getBaseDir() . '/../../..';
    }

    private function extractVersion()
    {
        $runResult = $this->cmdRunner->run('git describe --tags --exact-match HEAD');
        if (0 === $runResult->getExitCode()) {
            return trim($runResult->getStdOut());
        }

        $runResult = $this->cmdRunner->run('git -C \'' . $this->getBaseDir() . '\' log --pretty="%H" -n1 HEAD');

        if (0 !== $runResult->getExitCode()) {
            throw new \RuntimeException(
                'Can\'t run git log. You must ensure to run compile from artifact-downloader ' .
                'git repository clone and that git binary is available.'
            );
        }

        return trim($runResult->getStdOut());
    }

    /**
     * @return string
     */
    protected function getVersion()
    {
        if (null === $this->version) {
            $this->version = $this->extractVersion();
        }

        return $this->version;
    }

    /**
     * @return \DateTime
     */
    private function extractVersionDate()
    {
        $runResult = $this->cmdRunner->run('git -C \'' . $this->getBaseDir() . '\' log -n1 --pretty=%ci HEAD');
        if (0 !== $runResult->getExitCode()) {
            throw new \RuntimeException(
                'Can\'t run git log. You must ensure to run compile from ' .
                'artifact-downloader git repository clone and that git binary is available.'
            );
        }

        $result = new \DateTime(trim($runResult->getStdOut()));
        $result->setTimezone(new \DateTimeZone('UTC'));

        return $result;
    }

    /**
     * @return \DateTime
     */
    protected function getVersionDate()
    {
        if (null === $this->versionDate) {
            $this->versionDate = $this->extractVersionDate();
        }

        return $this->versionDate;
    }

    /**
     * @return Finder
     */
    private function makeBasicFinder()
    {
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                return strcmp(strtr($a->getRealPath(), '\\', '/'), strtr($b->getRealPath(), '\\', '/'));
            });

        return $finder;
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT), true)) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        return $output;
    }

    /**
     * @param \Phar        $phar
     * @param \SplFileInfo $file
     * @param bool         $strip
     */
    private function addFile(\Phar $phar, \SplFileInfo $file, $strip = true)
    {
        $path = str_replace(
            [realpath($this->getRootDir()) . DIRECTORY_SEPARATOR, '\\'],
            ['', '/'],
            $file->getRealPath()
        );

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        if ($path === 'src/CodeTool/ArtifactDownloader/ArtifactDownloader.php') {
            $content = strtr($content, [
                '@package_version@' => $this->getVersion(),
                '@release_date@' => $this->getVersionDate()->format('Y-m-d H:i:s')
            ]);
        }

        $phar->addFromString($path, $content);
    }

    private function addToolBinBin(\Phar $phar)
    {
        $content = file_get_contents($this->getRootDir() . '/bin/artifact-downloader.php');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $phar->addFromString('bin/artifact-downloader', $content);
    }

    private function getStub()
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php
/*
 * This file is part of ArtifactDownloader.
 */
Phar::mapPhar('artifact-downloader.phar');
EOF;
        // add warning once the phar is older than 60 days
        if (preg_match('{^[a-f0-9]+$}', $this->getVersion())) {
            $warningTime = ((int) $this->getVersionDate()->format('U')) + 60 * 86400;
            $stub .= "define('COMPOSER_DEV_WARNING_TIME', $warningTime);\n";
        }

        return $stub . <<<'EOF'
require 'phar://artifact-downloader.phar/bin/artifact-downloader';
__HALT_COMPILER();
EOF;
    }

    public function compile($pharFile = self::DEFAULT_PHAR_FILE)
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, self::DEFAULT_PHAR_FILE);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        $finder = $this->makeBasicFinder()
            ->name('*.php')
            ->notName('Compiler.php')
            ->in($this->getBaseDir() . '/../..');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $finder = $this->makeBasicFinder()
            ->name('*.php')
            ->name('LICENSE')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in($this->getRootDir() . '/vendor/psr/')
            ->in($this->getRootDir() . '/vendor/fool/')
            ->in($this->getRootDir() . '/vendor/pimple/');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/composer/autoload_psr4.php'));
        $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/composer/autoload_classmap.php'));
        $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/composer/autoload_real.php'));

        if (file_exists($this->getRootDir() . '/vendor/composer/include_paths.php')) {
            $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/composer/include_paths.php'));
        }
        $this->addFile($phar, new \SplFileInfo($this->getRootDir() . '/vendor/composer/ClassLoader.php'));

        $this->addToolBinBin($phar);

        // Stubs
        $phar->setStub($this->getStub());
        $phar->stopBuffering();

        // re-sign the phar with reproducible timestamp / signature
        $util = new Timestamps($pharFile);
        $util->updateTimestamps($this->versionDate);
        $util->save($pharFile, \Phar::SHA1);

        @chmod($pharFile, 0755);
    }
}
