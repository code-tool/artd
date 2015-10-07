<?php

namespace CodeTool\ArtifactDownloader\EtcdClient\Error\Details\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObjectInterface;
use CodeTool\ArtifactDownloader\EtcdClient\Error\Details\EtcdClientErrorDetails;

class EtcdClientErrorDetailsFactory implements EtcdClientErrorDetailsFactoryInterface
{
    /**
     * @param DomainObjectInterface $do
     *
     * @return EtcdClientErrorDetails
     */
    public function makeFromDo(DomainObjectInterface $do)
    {
        return new EtcdClientErrorDetails(
            $do->get(self::CAUSE_F_NAME),
            $do->get(self::ERROR_CODE_F_NAME),
            $do->get(self::INDEX_F_NAME),
            $do->get(self::MESSAGE_F_NAME)
        );
    }
}
