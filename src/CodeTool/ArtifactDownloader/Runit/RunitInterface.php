<?php

namespace CodeTool\ArtifactDownloader\Runit;

interface RunitInterface
{
    const STATE_STOPPED = 'stopped';

    const STATE_STARTED = 'started';

    const STATE_RESTARTED = 'restarted';

    /**
     * @param string $name
     * @param string $state
     *
     * @return \CodeTool\ArtifactDownloader\Result\ResultInterface
     */
    public function setServiceState($name, $state);
}
