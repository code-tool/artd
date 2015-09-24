<?php

namespace CodeTool\ArtifactDownloader\UnitStatusBuilder;

class UnitStatusBuilder implements UnitStatusBuilderInterface
{
    const MAX_ERRORS_COUNT = 10;

    private $status = 'undefined';

    private $errors = [];

    private $configVersion = 'undefined';

    public function addError($error)
    {
        $this->errors[] = $error;

        if (count($this->errors) > self::MAX_ERRORS_COUNT) {
            array_shift($this->errors);
        }

        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setConfigVersion($configVersion)
    {
        $this->configVersion = $configVersion;

        return $this;
    }

    public function build()
    {
        return json_encode([
            'ts' => time(),
            'status' => $this->status,
            'last_errors' => array_reverse($this->errors),
            'config_version' => $this->configVersion
        ]);
    }
}
