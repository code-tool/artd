<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Command\Factory\CommandFactoryInterface;
use CodeTool\ArtifactDownloader\Result\Factory\ResultFactoryInterface;
use CodeTool\ArtifactDownloader\Scope\Config\ScopeConfigRuleInterface;
use CodeTool\ArtifactDownloader\Scope\Info\ScopeInfoInterface;
use CodeTool\ArtifactDownloader\Util\BasicUtil;

class ScopeConfigProcessorRuleTypeSymlinkHandler implements ScopeConfigProcessorRuleTypeHandlerInterface
{
    /**
     * @var BasicUtil
     */
    private $basicUtil;

    /**
     * @var ResultFactoryInterface
     */
    private $resultFactory;

    /**
     * @var CommandFactoryInterface
     */
    private $commandFactory;

    /**
     * @param BasicUtil               $basicUtil
     * @param ResultFactoryInterface  $resultFactory
     * @param CommandFactoryInterface $commandFactory
     */
    public function __construct(
        BasicUtil $basicUtil,
        ResultFactoryInterface $resultFactory,
        CommandFactoryInterface $commandFactory
    ) {
        $this->basicUtil = $basicUtil;
        $this->resultFactory = $resultFactory;
        $this->commandFactory = $commandFactory;
    }

    /**
     * @return string[]
     */
    public function getSupportedTypes()
    {
        return ['symlink'];
    }

    /**
     * @param CommandCollectionInterface $collection
     * @param ScopeInfoInterface         $scopeInfo
     * @param ScopeConfigRuleInterface   $scopeConfigRule
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    public function buildCollection(
        CommandCollectionInterface $collection,
        ScopeInfoInterface $scopeInfo,
        ScopeConfigRuleInterface $scopeConfigRule
    ) {
        $realNamePath = $scopeInfo->getAbsPathByForTarget($scopeConfigRule->get('name'));
        $realTargetPath = $scopeInfo->getAbsPathByForTarget($scopeConfigRule->get('target'));

        if (false === file_exists($realNamePath) && false === is_link($realNamePath)) {
            // link or other file object dose not exists on same path
            $collection->add($this->commandFactory->createSymlinkCommand($realNamePath, $realTargetPath));

            return $this->resultFactory->createSuccessful();
        }

        if (false === is_link($realNamePath)) {
            $relativeTmpPath = $this->basicUtil->getRelativeTmpPath($realNamePath);
            // Target is not symlink
            $collection
                ->add($this->commandFactory->createRenameCommand($realNamePath, $relativeTmpPath))
                ->add($this->commandFactory->createSymlinkCommand($realNamePath, $realTargetPath))
                ->add($this->commandFactory->createRmCommand($relativeTmpPath));

            return $this->resultFactory->createSuccessful();
        }

        if ($realTargetPath !== readlink($realNamePath)) {
            // Link with same name already exists, but reference to other path
            $collection
                ->add($this->commandFactory->createRmCommand($realNamePath))
                ->add($this->commandFactory->createSymlinkCommand($realNamePath, $realTargetPath));

            return $this->resultFactory->createSuccessful();
        }

        return $this->resultFactory->createSuccessful();
    }
}
