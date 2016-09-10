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
            $token = $this->resolveToken($seg);

            if (gettype($token) === 'string') {
                $pattern .= sprintf('\/(%s)', $token);
            } else {
                if ($this->isExistType($token['type']) === false) {
                    throw new TypePatternNotFound('[' . $token['type'] . '] type is undefined');
                }

                $pattern .= sprintf('\/(%s)', $this->typeRegex[$token['type']]);
            }
        }

        return $pattern;
    }

    /**
     * Extract name and format type using $str
     * if $str is plain text url form, return $str.
     * otherwise, return below:
     * array['name']    string name
     *      ['type']    string type
     *
     *
     * @param $str
     * @return array|string
     */
    private function resolveToken($str)
    {
        // plain text url segment
        if ($this->isMatchingToken($str) === false) {
            return $str;

        // {name} url form
        } elseif (preg_match('/\{(\w+?)\}/', $str, $result)) {
            return [
                'name' => $result[1],
                'type' => 'string'
            ];

        // {name:type} url form
        } elseif (preg_match('/\{(\w+?):(\w+?)\}/', $str, $result)) {
            return [
                'name' => $result[1],
                'type' => $result[2]
            ];
        }
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


    private function isExistType($type)
    {
        return array_key_exists($type, $this->typeRegex);
    }
}
