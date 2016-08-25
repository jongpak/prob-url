# Prob/Url
*A simple library for handling path of url and matching url easily*

[![Build Status](https://travis-ci.org/jongpak/prob-url.svg?branch=master)](https://travis-ci.org/jongpak/prob-url)
[![codecov](https://codecov.io/gh/jongpak/prob-url/branch/master/graph/badge.svg)](https://codecov.io/gh/jongpak/prob-url)

## Usage

### Simple path parser
*Path class is separating url segments*
```php
<?php

use Prob\Url\Path;

$path = new Path('/apple/banana');

echo $path->seg(0);             // apple
echo $path->seg(1);             // banana

print_r($path->segments());     // Array ( 'apple', 'banana' )
```

### Simple url matching
*Matchar class is checking the url matching*

```php
<?php

use Prob\Url\Matcher;

$pathA = new Matcher('/apple');
print_r($pathA->match('/apple'));         // Array ( )
print_r($pathA->match('/banana'));        // false

$pathB = new Matcher('/apple/banana');
print_r($pathB->match('/apple/banana'));  // Array ( )
print_r($pathB->match('/apple'));         // false
print_r($pathB->match('/banana'));        // false

$pathC = new Matcher('/{board}/{post:int}');
print_r($pathC->match('/free/5'));        // Array ( 'board' => 'free', 'post' => '5' )
print_r($pathC->match('/free'));          // false
print_r($pathC->match('/free/some'));     // false
```

#### support type
  - string
  - int
