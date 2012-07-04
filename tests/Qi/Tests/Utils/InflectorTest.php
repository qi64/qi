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
        $s = 'ãéàêç';
        $this->assertEquals($s, Inflector::dasherize($s));
    }

    public function testNormalizeAcentos()
    {
        $s = 'áàãâ éèê íìî óòõô úùû ÁÀÃÂ ÉÈÊ ÍÌÎ ÓÒÕÔ ÚÙÛ';
        $e = 'aaaa eee iii oooo uuu AAAA EEE III OOOO UUU';
        $this->assertEquals($e, Inflector::normalize($s));
    }

    public function testNormalizeCedilha()
    {
        $s = 'ç Ç';
        $e = 'c C';
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
        $s = 'ÃÉÀÊÇ';
        $expected = 'ãéàêç';
        $this->assertEquals($expected, Inflector::lower($s));
    }

    public function testSlugize()
    {
        $s = 'Nome: São João, Maringá - PR. 04/01/1981.';
        $e = 'nome-sao-joao-maringa-pr-04-01-1981';
        $this->assertEquals($e, Inflector::slugize($s));
    }

    /**
     * should remove spaces before and after
     */
    public function testDasherizeSpaces()
    {
        $s = ' - name - ';
        $e = 'name';
        $this->assertEquals($e, Inflector::dasherize($s));
    }

    public function testUnderscore()
    {
        $s = 'CamelCasedWord';
        $e = 'Camel_Cased_Word';
        $this->assertEquals($e, Inflector::underscore($s));
    }

    public function testUnderscoreAcronymBegin()
    {
        $s = 'PHPCamelCasedWord';
        $e = 'PHP_Camel_Cased_Word';
        $this->assertEquals($e, Inflector::underscore($s));
    }

    public function testUnderscoreAcronymMiddle()
    {
        $s = 'CamelPHPCasedWord';
        $e = 'Camel_PHP_Cased_Word';
        $this->assertEquals($e, Inflector::underscore($s));
    }

    public function testUnderscoreAcronymEnd()
    {
        $s = 'CamelCasedWordPHP';
        $e = 'Camel_Cased_Word_PHP';
        $this->assertEquals($e, Inflector::underscore($s));
    }

    public function testUnderscoreNumbers()
    {
        $s = 'Camel23Cased42Word';
        $e = 'Camel23_Cased42_Word';
        $this->assertEquals($e, Inflector::underscore($s));
    }

    public function testTableize()
    {
        $s = 'My Table2Name';
        $e = 'my_table2_name';
        $this->assertEquals($e, Inflector::tableize($s));
    }
}
