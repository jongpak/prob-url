<?php

namespace Prob\Url;

use \ArrayIterator;
use Prob\Url\Exception\TypePatternNotFound;
use Prob\Url\TokenResolver;

class Matcher
{
    /**
     * @var array
     */
    private $typeRegex = [
        'int' => '\d+?',
        'string' => '\w+?',
    ];

    private $urlFormat = '';

    /**
     * Set URL Format
     * @see $this->typeRegex variable
     */
    public function setUrlFormat($urlFormat)
    {
        $this->urlFormat = $urlFormat;
    }

    /**
     * Add type and matching pattern
     * @param string $type name of type
     * @param string $pattern regex pattern of type
     */
    public function addTypePattern($type, $pattern)
    {
        $this->typeRegex[$type] = $pattern;
    }

    /**
     * @param $path
     * @return array|bool if not matched, return false. else, return placeholder name and value
     */
    public function getMatchedUrlFormat($path)
    {
        if ($this->isMatch($path) === false) {
            return null;
        }

        $pathVariableIterator = new ArrayIterator($this->getMatchingPathVariable($path));
        $matchedToken = [];

        foreach ($this->getMatcingToken($path) as $token) {
            // {name} or {name:type} segment
            if (gettype($pathVariableIterator->current()) === 'array') {
                $matchedToken[$pathVariableIterator->current()['name']] = $token;
            }
            $pathVariableIterator->next();
        }

        return $matchedToken;
    }

    public function isMatch($path)
    {
        return preg_match('/^' . $this->getMatchingRegexPattern() . '$/', $path)
                ? true
                : false;
    }

    private function getMatcingToken($path)
    {
        if ($this->isMatch($path) === false) {
            return [];
        }

        $result = [];
        preg_match('/^' . $this->getMatchingRegexPattern() . '$/', $path, $result);
        unset($result[0]);

        return $result;
    }

    private function getMatchingRegexPattern()
    {
        $resolver = new TokenResolver();
        $resolver->setTypeRegex($this->typeRegex);

        return $resolver->resolvePathPattern(new Path($this->urlFormat));
    }

    private function getMatchingPathVariable()
    {
        $resolver = new TokenResolver();
        $resolver->setTypeRegex($this->typeRegex);

        return $resolver->resolvePathVariable(new Path($this->urlFormat));
    }
}
