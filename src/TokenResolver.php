<?php

namespace Prob\Url;

use \ArrayIterator;
use Prob\Url\Exception\TypePatternNotFound;

class TokenResolver
{
    private $typeRegex = [];

    public function setTypeRegex(array $typeRegex)
    {
        $this->typeRegex = $typeRegex;
    }

    public function resolvePathVariable(Path $urlPath)
    {
        $resolvedToken = [];

        foreach ($urlPath->segments() as $seg) {
            $resolvedToken[] = $this->resolveToken($seg);
        }

        return $resolvedToken;
    }

    public function resolvePathPattern(Path $urlPath)
    {
        $pattern = count($urlPath->segments()) === 0 ? '(\/)' : '';

        foreach ($urlPath->segments() as $seg) {
            /** @var UrlPathToken */
            $token = $this->resolveToken($seg);

            $this->validateType($token->getType());

            $pattern .= sprintf('\/(%s)', $token->getType() === null
                                ? $token->getName()
                                : $this->typeRegex[$token->getType()]);
        }

        return $pattern;
    }

    /**
     * @param $str
     * @return UrlPathToken
     */
    private function resolveToken($str)
    {
        $urlPathToken = new UrlPathToken();

        // plain text url segment
        if ($this->isMatchingToken($str) === false) {
            $urlPathToken->setName($str);

        // {name} url form
        } elseif (preg_match('/\{(\w+?)\}/', $str, $result)) {
            $urlPathToken->setType('string');
            $urlPathToken->setName($result[1]);

        // {name:type} url form
        } elseif (preg_match('/\{(\w+?):(\w+?)\}/', $str, $result)) {
            $urlPathToken->setType($result[2]);
            $urlPathToken->setName($result[1]);
        }

        return $urlPathToken;
    }

    /**
     * checking {name} or {name:type} format
     *
     * @param $str
     * @return bool
     */
    private function isMatchingToken($str)
    {
        return preg_match('/\{(\w+?)\}/', $str) || preg_match('/\{(\w+?):(\w+?)\}/', $str);
    }

    private function validateType($type)
    {
        if ($type === null) {
            return;
        }

        if (array_key_exists($type, $this->typeRegex) === false) {
            throw new TypePatternNotFound('[' . $type . '] type is undefined');
        }
    }
}
