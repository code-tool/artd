<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error\Details\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\EtcdClientErrorDetailsInterface;

interface EtcdClientErrorDetailsFactoryInterface
{
    const CAUSE_F_NAME = 'cause';

    const ERROR_CODE_F_NAME = 'errorCode';

    const INDEX_F_NAME = 'index';

    const MESSAGE_F_NAME = 'message';

    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientErrorDetailsInterface
     */
    public function makeFromDo(DomainObjectInterface $do);
}
