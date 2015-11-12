<?php

namespace CodeTool\ArtifactDownloader\Command\Collection;

use CodeTool\ArtifactDownloader\Command\CommandInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Result\ResultInterface;
use Psr\Log\LoggerInterface;

class CommandCollection implements CommandCollectionInterface
{
    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CommandInterface[]
     */
    private $commands = [];

    /**
     * @param ResultFactoryInterface $resultFactory
     * @param LoggerInterface        $logger
     */
    public function __construct(ResultFactoryInterface $resultFactory, LoggerInterface $logger)
    {
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
    }

    /**
     * @param CommandInterface $command
     *
     * @return CommandCollectionInterface
     */
    public function add(CommandInterface $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        foreach ($this->commands as $command) {
            $this->logger->debug(sprintf('Executing %s', $command));
            $result = $command->execute();
            if (false === $result->isSuccessful()) {
                return $result;
            }
        }

        return $this->resultFactory->createSuccessful();
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->commands);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = [];

        foreach ($this->commands as $command) {
            $result[] = sprintf("\t%s", $command);
        }

        return implode(PHP_EOL, $result);
    }
}
