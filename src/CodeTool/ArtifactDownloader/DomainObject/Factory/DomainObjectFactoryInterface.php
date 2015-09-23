<?php

namespace CodeTool\ArtifactDownloader\DomainObject\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;

interface DomainObjectFactoryInterface
{
    /**
     * @param array $data
     *
     * @return DomainObjectInterface
     */
    public function makeFromArray(array $data);

    /**
     * @param array $data
     *
     * @return DomainObjectInterface
     */
    public function makeRecursiveFromArray(array $data);
}
