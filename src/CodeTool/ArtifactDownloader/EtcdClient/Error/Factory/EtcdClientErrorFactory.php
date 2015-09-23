<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientError;
use CodeTool\ArtifactDownloader\EtcdClient\Error\EtcdClientErrorInterface;

class EtcdClientErrorFactory implements EtcdClientErrorFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientErrorInterface
     */
    public function makeFromDo(DomainObjectInterface $do)
    {
        return new EtcdClientError(
            $do->get(self::CAUSE_F_NAME),
            $do->get(self::ERROR_CODE_F_NAME),
            $do->get(self::INDEX_F_NAME),
            $do->get(self::MESSAGE_F_NAME)
        );
    }
}
