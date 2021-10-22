[![Build Status](https://travis-ci.com/Mistralys/subsetsum.svg?branch=master)](https://travis-ci.com/Mistralys/subsetsum)

# PHP SubsetSum implementation

Given a target number and a list of numbers, determines which number combinations equal the target number.

For example: With `25` as the target number, and `10`, `5`, `15` as the numbers list, this will determine that `25 = 10 + 15`.

## Requirements

- PHP >= 7.1

## Installation

Require the package via composer on the command line:

```
composer require mistralys/subsetsum
```

Or edit composer.json directly:

```
"require": 
{
    "mistralys/subsetsum": "dev-master"
}
```

## Usage

The `create` method is used to create a new instance, which can be used to retrieve matches, or to configure options:

```php
$sub = SubsetSum::create(25, array(5,10,7,3,20));
```

### Checking if there are any matches

Some methods like `getShortestMatch()` can return null, so it's best to check if there are matches beforehand.

```php
if(!$sub->hasMatches())
{
    echo 'No matches.';
}
```

### Getting all matches

To retrieve all matching number combinations:

```php
$matches = $sub->getMatches();
```

This will return an array like this:

```php
array(
    array(3, 5, 7, 10),
    array(5, 20)
)
```

NOTE: The numbers in each match result are always sorted in ascending order.

### Getting the shortest match

The shortest match is the one that uses the least amount of number combinations.

```php
// check if there are matches, since the method can return null.
if($sub->hasMatches())
{
	$match = $sub->getShortestMatch();
}
```

In the example, this would return the following match array:

```
array(5, 20)
```

### Getting the longest match

The longest match is the one that uses the highest amount of number combinations.

```php
// check if there are matches, since the method can return null.
if($sub->hasMatches())
{
	$match = $sub->getLongestMatch();
}
```

In the example, this would return the following match array:

```
array(3, 5, 7, 10)
```

### Adjusting the amount of decimals & rounding

By default, the internal calculations will round the numbers to `2` decimals, using PHP's default "round up half" rounding. This can be easily adjusted to your needs:

```php
// 4 decimals, default rounding mode
$sub->setPrecision(4);

// 1 decimal, specific rounding mode
$sub->setPrecision(1, PHP_ROUND_HALF_DOWN);
```

The full list of possible modes can be found here:

http://www.php.net/manual/en/math.constants.php

### Working with integers

Working in integer mode simply means using a precision of `0`. 

```php
$sub->makeInteger();
```

NOTE: The match arrays will contain integers, but which are still typed as floats. You will have to cast them to `int` as needed.

## Performance

A word of caution: calculating subset sums has an exponential complexity the higher the amount of numbers to search through. You can easily bring your server to your knees with larger sets, so I would recommend setting some limits on the amount of numbers in your application.

## Credits

The initial mechanism was inspired by this answer on StackOverflow:

http://stackoverflow.com/questions/4632322/finding-all-possible-combinations-of-numbers-to-reach-a-given-sum/#answer-43351998

There is also another interesting package that goes further than this:

https://github.com/pipan/subsetsum-php
