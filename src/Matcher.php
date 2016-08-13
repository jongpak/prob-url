<?php

namespace Prob\Url;

use \ArrayIterator;

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
    private $urlRegexPattern = [];
    private $urlTokens = [];

    /**
     * Matcher constructor.
     * @param string $path url placeholder format of $this->$pattern
     * @see $this->pattern
     */
    public function __construct($path)
    {
        $urlPath = new Path($path);
        $this->urlSegment = $urlPath->segments();

        $this->interpret();
    }

    /**
     * @param $path
     * @return array|bool if not matched, return false. else, return placeholder name and value
     */
    public function match($path)
    {
        $pattern = new ArrayIterator($this->urlTokens);
        $isMatch = preg_match('/^' . $this->urlRegexPattern . '$/', $path, $result);

        if ($isMatch === 0)
            return false;

        $resolveToken = [];
        unset($result[0]);

        foreach ($result as $token) {
            // {name} or {name:type} segment
            if(gettype($pattern->current()) === 'array')
                $resolveToken[$pattern->current()['name']] = $token;
            $pattern->next();
        }

        return $resolveToken;
    }

    private function interpret()
    {
        $pattern = '(\/)';

        if(count($this->urlSegment) > 0) {
            $pattern = '';

            foreach ($this->urlSegment as $seg) {
                $token = $this->translateToken($seg);
                $this->urlTokens[] = $token;

                if(gettype($token) === 'string')
                    $pattern .= sprintf('\/(%s)', $token);
                else
                    $pattern .= sprintf('\/(%s)', $this->typeRegex[$token['type']]);
            }
        }

        $this->urlRegexPattern = $pattern;
    }

    /**
     * @param $str
     * @return array|string
     */
    private function translateToken($str)
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
