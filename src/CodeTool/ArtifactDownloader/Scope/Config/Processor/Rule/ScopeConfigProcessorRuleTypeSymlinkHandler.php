<?php

namespace CodeTool\ArtifactDownloader\Scope\Config\Processor\Rule;

use CodeTool\ArtifactDownloader\Command\Collection\CommandCollectionInterface;
use CodeTool\ArtifactDownloader\Fs\Command\Factory\FsCommandFactoryInterface;
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
     * @var FsCommandFactoryInterface
     */
    private $fsCommandFactory;

    /**
     * @param BasicUtil                 $basicUtil
     * @param ResultFactoryInterface    $resultFactory
     * @param FsCommandFactoryInterface $fsCommandFactory
     */
    public function __construct(
        BasicUtil $basicUtil,
        ResultFactoryInterface $resultFactory,
        FsCommandFactoryInterface $fsCommandFactory
    ) {
        $this->basicUtil = $basicUtil;
        $this->resultFactory = $resultFactory;
        $this->fsCommandFactory = $fsCommandFactory;
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
            $collection->add($this->fsCommandFactory->createSymlinkCommand($realNamePath, $realTargetPath));

            return $this->resultFactory->createSuccessful();
        }

        if (false === is_link($realNamePath)) {
            $relativeTmpPath = $this->basicUtil->getRelativeTmpPath($realNamePath);
            // Target is not symlink
            $collection
                ->add($this->fsCommandFactory->createRenameCommand($realNamePath, $relativeTmpPath))
                ->add($this->fsCommandFactory->createSymlinkCommand($realNamePath, $realTargetPath))
                ->add($this->fsCommandFactory->createRmCommand($relativeTmpPath));

            return $this->resultFactory->createSuccessful();
        }

        if ($realTargetPath !== readlink($realNamePath)) {
            // Link with same name already exists, but reference to other path
            $collection
                ->add($this->fsCommandFactory->createRmCommand($realNamePath))
                ->add($this->fsCommandFactory->createSymlinkCommand($realNamePath, $realTargetPath));

            return $this->resultFactory->createSuccessful();
        }

        return $this->resultFactory->createSuccessful();
    }
}
