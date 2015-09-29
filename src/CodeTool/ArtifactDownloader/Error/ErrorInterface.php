<?php
namespace CodeTool\ArtifactDownloader\Error;

interface ErrorInterface
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return mixed
     */
    public function getContext();

    /**
     * @return ErrorInterface|null
     */
    public function getPrevError();
}
