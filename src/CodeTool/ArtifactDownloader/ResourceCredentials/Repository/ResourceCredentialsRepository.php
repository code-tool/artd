<?php

namespace CodeTool\ArtifactDownloader\ResourceCredentials\Repository;

use CodeTool\ArtifactDownloader\ResourceCredentials\ResourceCredentialsInterface;

class ResourceCredentialsRepository implements ResourceCredentialsRepositoryInterface
{
    /**
     * @var ResourceCredentialsInterface[]
     */
    private $resourceCredentials;

    /**
     * @param ResourceCredentialsInterface[] $resourceCredentials
     */
    public function __construct(array $resourceCredentials = [])
    {
        $this->resourceCredentials = $resourceCredentials;
    }

    /**
     * @param string    $pattern
     * @param string    $value
     * @param bool|true $ignoreCase
     *
     * @return bool
     */
    private function isMatch($pattern, $value, $ignoreCase = true)
    {
        $expr = preg_replace_callback(
            '/[\\\\^$.[\\]|()?*+{}\\-\\/]/',
            function ($matches) {
                switch ($matches[0]) {
                    case '*':
                        return '.*';
                    case '?':
                        return '.';
                    default:
                        return '\\' . $matches[0];
                }
            },
            $pattern
        );

        $expr = '/' . $expr . '/';
        if (true === $ignoreCase) {
            $expr .= 'i';
        }

        return (bool) preg_match($expr, $value);
    }

    /**
     * @param string $url
     *
     * @return ResourceCredentialsInterface|null
     */
    public function getCredentialsByResourcePath($url)
    {
        if (false === $parsedUrl = parse_url($url)) {
            return null;
        }

        $scheme = $parsedUrl['scheme'];

        if (false === array_key_exists('port', $parsedUrl)) {
            $port = getservbyname($scheme, 'tcp');
        } else {
            $port = $parsedUrl['port'];
        }

        foreach ($this->resourceCredentials as $resourceCredentials) {
            if ($this->isMatch($resourceCredentials->getPort(), $port) &&
                $this->isMatch($resourceCredentials->getScheme(), $scheme) &&
                $this->isMatch($resourceCredentials->getHost(), $parsedUrl['host'])
            ) {
                return $resourceCredentials;
            }
        }

        return null;
    }
}
