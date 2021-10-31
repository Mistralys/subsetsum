<?php
/**
 * File containing the {@see SubsetSum} class.
 * 
 * @package SubsetSum
 * @see SubsetSum
 */

declare(strict_types=1);

namespace Mistralys\SubsetSum;

/**
 * Calculates a subset sum: finds out which combinations of numbers
 * from a list of numbers can be used to reach the target number.
 *
 * Example:
 *
 * <pre>
 * SubsetSum::create(25, array(5,10,7,3,20))->getMatches();
 * </pre>
 *
 * Returns:
 *
 * <pre>
 * Array
 *(
 *   [0] => Array
 *   (
 *       [0] => 3
 *       [1] => 5
 *       [2] => 7
 *       [3] => 10
 *   )
 *   [1] => Array
 *   (
 *       [0] => 5
 *       [1] => 20
 *   )
 *)
 *</pre>
 * 
 * @package SubsetSum
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class SubsetSum
{
    const ERROR_NEGATIVE_PRECISION = 67701;
    
    /**
     * @var float
     */
    private $targetSum;
    
    /**
     * @var float[]
     */
    private $numbersStack;
    
    /**
     * @var array<int,array<int,float>>
     */
    private $matches = array();
    
   /**
    * The amount of decimals to round up to.
    * @var integer
    */
    private $precision = 2;
    
   /**
    * The mode with which to round decimals to the target precision.
    * @var int
    */
    private $roundMode = PHP_ROUND_HALF_UP;
    
   /**
    * @var boolean
    */
    private $calculated = false;

   /**
    * @param float $targetSum
    * @param array<int,float> $numbersStack
    */
    private function __construct(float $targetSum, array $numbersStack)
    {
        $this->targetSum = $targetSum;
        $this->numbersStack = $this->filterStack($numbersStack);
    }
    
   /**
    * Creates a SubsetSum instance, and analyses the specified numbers.
    *
    * @param float $targetSum The number to search for.
    * @param array<int,float> $numbersStack The stack of numbers to search in.
    */
    public static function create(float $targetSum, array $numbersStack) : SubsetSum
    {
        return (new SubsetSum($targetSum, $numbersStack));
    }

    /**
     * Sets the amount of decimals to use in the calculations. Numbers
     * will be rounded to the specified amount of decimals, using the
     * rounding mode.
     *
     * @param int $precision The amount of decimals.
     * @param int $mode The target rounding mode.
     * @return SubsetSum
     *
     * @throws SubsetSum_Exception
     * @see SubsetSum::ERROR_NEGATIVE_PRECISION
     *
     * @link http://www.php.net/manual/en/math.constants.php
     */
    public function setPrecision(int $precision, int $mode=PHP_ROUND_HALF_UP) : SubsetSum
    {
        if($precision < 0)
        {
            throw new SubsetSum_Exception(
                'Invalid precision: must be a positive integer.',
                self::ERROR_NEGATIVE_PRECISION
            );
        }
        
        $this->precision = $precision;
        $this->roundMode = $mode;
        
        // ensure that the calculations are run anew after this
        $this->resetCalculation();
        
        return $this;
    }
    
    public function getSum() : float
    {
        return $this->convert($this->targetSum);
    }

    /**
     * Sets the precision to integers.
     *
     * @param int $roundMode
     * @return SubsetSum
     *
     * @throws SubsetSum_Exception
     * @see SubsetSum::ERROR_NEGATIVE_PRECISION
     */
    public function makeInteger(int $roundMode=PHP_ROUND_HALF_UP) : SubsetSum
    {
        return $this->setPrecision(0, $roundMode);
    }
    
   /**
    * Retrieves all matches that were found.
    * 
    * @return array<int,array<int,float>>
    */
    public function getMatches() : array
    {
        $this->calculate();
        
        return $this->matches;
    }
    
   /**
    * Checks whether any matches were found.
    * 
    * @return bool
    */
    public function hasMatches() : bool
    {
        $this->calculate();
        
        return !empty($this->matches);
    }
    
   /**
    * Retrieves the match with the least amount of numbers.
    * 
    * @return array<int,float>|NULL The match, or null if there were no matches.
    */
    public function getShortestMatch()
    {
        $this->calculate();
        
        if(empty($this->matches))
        {
            return null;
        }

        $list = $this->getCounts();
        $min = min(array_keys($list));

        return array_shift($list[$min]);
    }
    
   /**
    * Retrieves the match with the highest amount of numbers.
    * 
    * @return array<int,float>|NULL The match, or null if there were no matches.
    */
    public function getLongestMatch()
    {
        $this->calculate();
        
        if(empty($this->matches))
        {
            return null;
        }
        
        $list = $this->getCounts();
        $max = max(array_keys($list));
        
        return array_shift($list[$max]);
    }
    
    private function calculate() : void
    {
        if($this->calculated)
        {
            return;
        }
        
        $this->calculated = true;
        
        $this->matches = array();
        
        // only try to find a subset if it makes sense.
        if($this->targetSum > 0 && !empty($this->numbersStack))
        {
            $this->searchRecursive($this->convertArray($this->numbersStack));
        }
    }
    
    private function resetCalculation() : void
    {
        $this->calculated = false;
    }
    
   /**
    * Rounds the specified number to the target precision.
    * 
    * @param float $number
    * @return float
    */
    private function convert(float $number) : float
    {
        return round($number, $this->precision, $this->roundMode);
    }
    
   /**
    * Rounds an array of numbers to the target precision.
    * 
    * @param array<int,float> $numbers
    * @return array<int,float>
    */
    private function convertArray(array $numbers) : array
    {
        $result = array();
        
        foreach($numbers as $idx => $number)
        {
            $result[$idx] = $this->convert($number);
        }
        
        return $result;
    }
    
   /**
    * Filters the stack of numbers to ensure they are all
    * positive numbers. Negative numbers are converted to
    * positive. Zero values are pruned out.
    * 
    * @param array<int,float> $stack
    * @return array<int,float>
    */
    private function filterStack(array $stack) : array
    {
        $keep = array();
        
        foreach($stack as $number)
        {
            if($number == 0)
            {
                continue;
            }
            
            if($number < 0)
            {
                $number = $number * -1;
            }
            
            $keep[] = floatval($number); 
        }
        
        return $keep;
    }
    
   /**
    * Retrieves all matches, categorized by the amount 
    * of numbers in each match.
    * 
    * @return array<int,array<int,array<int,float>>>
    */
    private function getCounts() : array
    {
        $result = array();
        
        foreach($this->matches as $match)
        {
            $amount = count($match);
            
            if(!isset($result[$amount]))
            {
                $result[$amount] = array();
            }
            
            $result[$amount][] = $match;
        }
        
        ksort($result);
        
        return $result;
    }
    
   /**
    * Recursively analyzes the specified numbers to see if their
    * sum equals the target number.
    * 
    * @param array<int,float> $numbers Target stack of numbers to reach.
    * @param array<int,float> $currentStack Current combination we're trying.
    */
    private function searchRecursive(array $numbers, array $currentStack=array()) : void
    {
        $s = array_sum($currentStack);
        $search = $this->getSum();
        
        // we have found a match!
        if(bccomp((string) $s, (string) $search, $this->precision) === 0)
        {
            sort($currentStack); // ensure the numbers are always sorted
            
            $this->matches[] = $currentStack;
            return;
        }
        
        // gone too far, break off
        if($s >= $search)
        {
            return;
        }
        
        $totalNumbers = count($numbers);
        
        for($i=0; $i < $totalNumbers; $i++)
        {
            $remaining = array();
            $number = $numbers[$i];
            
            for($j = $i+1; $j < $totalNumbers; $j++) {
                $remaining[] = $numbers[$j];
            }
            
            $newStack = $currentStack;
            $newStack[] = $number;
            
            // recursively try to match this new stack of numbers.
            $this->searchRecursive($remaining, $newStack);
        }
    }
}
