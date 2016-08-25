<?php

namespace Prob\Url;

use \ArrayIterator;
use Prob\Url\Exception\TypePatternNotFound;

class Matcher
{
    /**
     * @var array
     */
    private $typeRegex = [
        'int' => '\d+?',
        'string' => '\w+?',
    ];

    private $urlSegment = [];

    /**
     * Matcher constructor.
     * @param string $path url placeholder format of $this->$pattern
     * @see $this->pattern
     */
    public function __construct($path)
    {
        $urlPath = new Path($path);
        $this->urlSegment = $urlPath->segments();
    }

    /**
     * @param $path
     * @return array|bool if not matched, return false. else, return placeholder name and value
     */
    public function match($path)
    {
        $pattern = new ArrayIterator($this->getResolvedToken());
        $isMatch = preg_match('/^' . $this->getMatchingRegexPattern() . '$/', $path, $result);

        if ($isMatch === 0) {
            return false;
        }

        $matchedToken = [];
        unset($result[0]);

        foreach ($result as $token) {
            // {name} or {name:type} segment
            if (gettype($pattern->current()) === 'array') {
                $matchedToken[$pattern->current()['name']] = $token;
            }
            $pattern->next();
        }

        return $matchedToken;
    }

    private function getResolvedToken()
    {
        $resolvedToken = [];

        foreach ($this->urlSegment as $seg) {
            $resolvedToken[] = $this->resolveToken($seg);
        }

        return $resolvedToken;
    }

    private function getMatchingRegexPattern()
    {
        $pattern = count($this->urlSegment) === 0 ? '(\/)' : '';

        foreach ($this->urlSegment as $seg) {
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

    private function isExistType($type)
    {
        return array_key_exists($type, $this->typeRegex);
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
}
