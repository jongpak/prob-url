<?php

namespace Prob\Url;

class Path
{
    private $path = '';
    private $seg = [];

    public function __construct($path = '')
    {
        $this->path = $path;
        $this->separate();
    }

    public function seg($index)
    {
        return $this->seg[$index];
    }

    public function segments()
    {
        return $this->seg;
    }

    private function separate()
    {
        $seg = [];
        $token = strtok($this->path, '/');

        while ($token !== false) {
            $seg[] = $token;
            $token = strtok('/');
        }

        $this->seg = $seg;
    }
}
