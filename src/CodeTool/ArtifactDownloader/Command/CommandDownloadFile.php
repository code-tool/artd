<?php

namespace CodeTool\ArtifactDownloader\Command;

use CodeTool\ArtifactDownloader\Command\Result\CommandResultInterface;

class CommandDownloadFile implements CommandInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $target;

    /**
     * @param string $url
     * @param string $target
     */
    public function __construct($url, $target)
    {
        $this->url = $url;
        $this->target = $target;
    }

    /**
     * @return CommandResultInterface
     */
    public function execute()
    {
        $targetFileHandle = fopen($this->target, 'w+');

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $targetFileHandle);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch); // get curl response
        curl_close($ch);

        fclose($targetFileHandle);
    }
}
