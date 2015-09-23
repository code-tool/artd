<?php

namespace CodeTool\ArtifactDownloader\UnitStatusBuilder;

interface UnitStatusBuilderInterface
{
    /**
     * @param string $error
     *
     * @return UnitStatusBuilderInterface
     */
    public function addError($error);

    /**
     * @param string $status
     *
     * @return UnitStatusBuilderInterface
     */
    public function setStatus($status);

    /**
     * @param string $configVersion
     *
     * @return UnitStatusBuilderInterface
     */
    public function setConfigVersion($configVersion);

    /**
     * @return string
     */
    public function build();
}
