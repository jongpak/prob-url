<?php

namespace Prob\Url;

use \ArrayIterator;

class Matcher
{
    /**
     * @var array
     */
    private $pattern = [
        'int' => '\d+?',
        'string' => '\w+?',
    ];

    private $urlSegment = [];
    private $urlPattern = [];
    private $urlTokens = [];

    /**
     * Matcher constructor.
     * @param string $pathPattern url placeholder pattern of $this->$pattern
     * @see $this->pattern
     */
    public function __construct($pathPattern)
    {
        $urlPath = new Path($pathPattern);
        $this->urlSegment = $urlPath->segments();

        $this->interpret();
    }

    /**
     * @param $path
     * @return array|bool if not matched, return false. else, return placeholder name and value
     */
    public function match($path)
    {
        $result = [];
        $resolveToken = [];

        $isMatch = preg_match('/^' . $this->urlPattern . '$/', $path, $result);
        unset($result[0]);

        if ($isMatch === 0)
            return false;

        $pattern = new ArrayIterator($this->urlTokens);

        foreach ($result as $token) {
            if ($pattern->current()['name']) {
                $resolveToken[$pattern->current()['name']] = $token;
            }
            $pattern->next();
        }

        return $resolveToken;
    }

    private function interpret()
    {
        $token = [];
        $pattern = '';

        if (count($this->urlSegment) === 0) {
            $pattern = '(\/)';
        } else {
            foreach ($this->urlSegment as $seg) {
                $token = $this->translateToken($seg);
                $this->urlTokens[] = $token;

                $pattern .= sprintf('\/(%s)', empty($token['name']) ? $token['pattern'] : $this->pattern[$token['pattern']]);
            }
        }

        $this->urlPattern = $pattern;
    }

    /**
     * @param $str
     * @return array ['name', 'pattern' => regex]
     */
    private function translateToken($str)
    {
        $trans = [
            'name' => '',
            'pattern' => ''
        ];

        if ($this->isMatchingToken($str) === false) {
            $trans['pattern'] = $str;
        } elseif (preg_match('/\{(\w+?)\}/', $str, $result)) {
            $trans['name'] = $result[1];
            $trans['pattern'] = 'string';
        } elseif (preg_match('/\{(\w+?):(\w+?)\}/', $str, $result)) {
            $trans['name'] = $result[1];
            $trans['pattern'] = $result[2];
        }

        return $trans;
    }

    /**
     * {name} or {name:patternHolder}
     *
     * @param $str
     * @return bool
     */
    private function isMatchingToken($str)
    {
        return preg_match('/\{(\w+?)\}/', $str) || preg_match('/\{(\w+?):(\w+?)\}/', $str);
    }
}