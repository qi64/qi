<?php

namespace Qi\Tests\Utils;
use Qi\Utils\Inflector;

class InflectorTest extends \PHPUnit_Framework_TestCase
{
    public function testDasherizeMultipleSpacesAndUnderscore()
    {
        $sentence = 'The:quick, 23   brown-fox___[jumps]_ _over _ the_ _
         _lazy _ _ dog.';
        $expected = 'The-quick-23-brown-fox-jumps-over-the-lazy-dog';
        $this->assertEquals($expected, Inflector::dasherize($sentence));
    }

    public function testDasherizeEncoding()
    {
        $s = 'ãéàê';
        $this->assertEquals($s, Inflector::dasherize($s));
    }

    public function testNormalize()
    {
        $s = 'áàãâ éèê íìî óòõô úùû ÁÀÃÂ ÉÈÊ ÍÌÎ ÓÒÕÔ ÚÙÛ';
        $e = 'aaaa eee iii oooo uuu AAAA EEE III OOOO UUU';
        $this->assertEquals($e, Inflector::normalize($s));
    }

    public function testDasherizeBeginEnd()
    {
        $sentence = '!sentence!';
        $expected = 'sentence';
        $this->assertEquals($expected, Inflector::dasherize($sentence));
    }

    public function testDasherizeCustomChar()
    {
        $sentence = 'My Name';
        $expected = 'My_Name';
        $this->assertEquals($expected, Inflector::dasherize($sentence, '_'));
    }

    public function testLower()
    {
        $s = 'ÃÉÀÊ';
        $expected = 'ãéàê';
        $this->assertEquals($expected, Inflector::lower($s));
    }

    public function testSlugize()
    {
        $s = 'Nome: São João, Maringá - PR. 04/01/1981.';
        $e = 'nome-sao-joao-maringa-pr-04-01-1981';
        $this->assertEquals($e, Inflector::slugize($s));
    }
}
