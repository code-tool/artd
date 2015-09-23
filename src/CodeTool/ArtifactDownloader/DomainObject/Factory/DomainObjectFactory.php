<?php

namespace CodeTool\ArtifactDownloader\DomainObject\Factory;

use CodeTool\ArtifactDownloader\DomainObject\DomainObject;

class DomainObjectFactory implements DomainObjectFactoryInterface
{
    public function makeFromArray(array $data)
    {
        return new DomainObject($data);
    }

    public function makeRecursiveFromArray(array $data)
    {
        array_walk($data, function (&$value) {
            if (false === is_array($value)) {
                return;
            }

            $value = $this->makeRecursiveFromArray($value);
        });

        return $this->makeFromArray($data);
    }
}
