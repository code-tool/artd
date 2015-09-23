<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;

interface EtcdClientErrorFactoryInterface
{
    const CAUSE_F_NAME = 'cause';

    const ERROR_CODE_F_NAME = 'errorCode';

    const INDEX_F_NAME = 'index';

    const MESSAGE_F_NAME = 'message';

    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientErrorInterface
     */
    public function makeFromDo(DomainObjectInterface $do);
}
