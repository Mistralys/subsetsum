<?php

use PHPUnit\Framework\TestCase;
use Mistralys\SubsetSum\SubsetSum;
use Mistralys\SubsetSum\SubsetSum_Exception;

final class Functions_SubsetSumTest extends TestCase
{
    public function test_getMatches_empty() : void
    {
        $obj = SubsetSum::create(10, array());
        
        $this->assertEquals(array(), $obj->getMatches());
        $this->assertFalse($obj->hasMatches());
    }
    
    public function test_getMatches_zero() : void
    {
        $obj = SubsetSum::create(0, array(5,10,11));
        
        $this->assertEquals(array(), $obj->getMatches());
        $this->assertFalse($obj->hasMatches());
    }
    
    public function test_getMatches_negative() : void
    {
        $obj = SubsetSum::create(-85, array(5,10,11));
        
        $this->assertEquals(array(), $obj->getMatches());
        $this->assertFalse($obj->hasMatches());
    }
    
    public function test_getMatches_negativeStack() : void
    {
        $obj = SubsetSum::create(15, array(5,10,-15));
        
        $expected = array(
            array(5, 10),
            array(15)
        );
        
        $this->assertEquals($expected, $obj->getMatches());
    }
    
    public function test_getMatches_zeroesInStack() : void
    {
        $obj = SubsetSum::create(15, array(5,10,0,15));
        
        $expected = array(
            array(5, 10),
            array(15)
        );
        
        $this->assertEquals($expected, $obj->getMatches());
    }
    
    public function test_getMatches() : void
    {
        $obj = SubsetSum::create(25, array(5,10,7,3,20));
        
        $result = $obj->getMatches();
        
        $expected = array(
            array(3, 5, 7, 10),
            array(5, 20)
        );
        
        $this->assertEquals($expected, $result);
    }
    
    public function test_getShortestMatch() : void
    {
        $obj = SubsetSum::create(25, array(5,10,7,3,20));
        
        $result = $obj->getShortestMatch();
        
        $expected = array(5, 20);
        
        $this->assertEquals($expected, $result);
    }
    
    public function test_getLongestMatch() : void
    {
        $obj = SubsetSum::create(25, array(5,10,7,3,20));
        
        $result = $obj->getLongestMatch();
        
        $expected = array(3, 5, 7, 10);
        
        $this->assertEquals($expected, $result);
    }
    
    public function test_precision_default() : void
    {
        $obj = SubsetSum::create(25.15, array(5.01,10.14,7,3,20));
        
        $expected = array(
            array(3, 5.01, 7, 10.14)
        );
        
        $this->assertEquals($expected, $obj->getMatches());
    }
    
   /**
    * Ensure that rounding to the default 2 decimals
    * works as expected.
    */
    public function test_precision_default_rounding() : void
    {
        $obj = SubsetSum::create(25.15, array(5.01243,10.143514,7,3,20));
        
        $expected = array(
            array(3, 5.01, 7, 10.14)
        );
        
        $this->assertEquals($expected, $obj->getMatches());
    }
    
    public function test_setInteger() : void
    {
        $obj = SubsetSum::create(25.15, array(5.01,10.14,7,3,20));
        $obj->makeInteger();
        
        $expected = array(
            array(3, 5, 7, 10),
            array(5, 20)
        );
        
        $this->assertEquals($expected, $obj->getMatches());
    }
    
    public function test_getSearch_round() : void
    {
        $obj = SubsetSum::create(25.1452341, array());
        
        $this->assertEquals(25.15, $obj->getSum());
    }
    
    public function test_getSearch_integer() : void
    {
        $obj = SubsetSum::create(25.15, array());
        $obj->makeInteger();
        
        $this->assertEquals(25, $obj->getSum());
        
    }
    
    public function test_precision_negative() : void
    {
        $obj = SubsetSum::create(25, array());
        
        try
        {
            $obj->setPrecision(-1);    
        }
        catch(SubsetSum_Exception $e)
        {
            $this->assertSame(SubsetSum::ERROR_NEGATIVE_PRECISION, $e->getCode());
        }
    }
}
