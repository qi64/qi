<?php

namespace Qi\Parser;

use SplFileInfo,
    ArrayIterator,
    RuntimeException;

/**
 * A diferença de implementação entre os bancos é muito grande, tornando praticamente inútil.
 * O FITID não é sempre único e as vezes falta o balance.
 */
class Ofx extends ArrayIterator
{
    public $OFXContent;
    public $originalString;
    public $moviments = array();
    public $conf;
    public $bank_id = "";
    public $branch_id = "";
    public $account_id = "";
    public $balance_amount = "";
    public $balance_date = "";

    public function __construct($string = null)
    {
        if ($string instanceof SplFileInfo) {
            $this->loadFromFile($string);
        }else{
            $this->loadFromString($string);
        }
        parent::__construct($this->getIndexedById());
    }

    public function loadFromFile($OFXFile)
    {
        if(file_exists($OFXFile))
            return $this->loadFromString(utf8_encode(file_get_contents($OFXFile)));
        else
            throw new RuntimeException("file not found '$OFXFile'");
    }

    public function loadFromString($OFXContent)
    {
        if (!$OFXContent) return;
        // fix preg_replace with m option does not recognize \r as line ending
        $OFXContent = str_replace("\r\n", "\n", $OFXContent);

        $this->originalString= $OFXContent;
        $this->OFXContent= $OFXContent;

        $this->OFXContent= explode('<OFX>', $this->OFXContent);
        $this->conf= $this->OFXContent[0];
        $this->OFXContent= '<OFX>'.$this->OFXContent[1];
        $this->fixTag('code');
        $this->fixTag('SEVERITY');
        $this->fixTag('CURDEF');
        $this->fixTag('DTSERVER');
        $this->fixTag('LANGUAGE');
        $this->fixTag('TRNUID');
        $this->fixTag('BANKID');
        $this->fixTag('ACCTID');
        $this->fixTag('ACCTTYPE');
        $this->fixTag('DTSTART');
        $this->fixTag('DTEND');
        $this->fixTag('TRNTYPE');
        $this->fixTag('DTPOSTED');
        $this->fixTag('TRNAMT');
        $this->fixTag('FITID');
        $this->fixTag('CHECKNUM');
        $this->fixTag('DTASOF');
        $this->fixTag('LEDGERBAL');
        $this->fixTag('BALAMT');
        $this->fixTag('LEDGERBAL');
        $this->fixTag('REFNUM');
        $this->fixTag('MEMO', true);
        /*
TRNTYPE
DTPOSTED
TRNAMT
FITID
CHECKNUM
REFNUM
MEMO
        */

        $this->ofx= simplexml_load_string("<?xml version='1.0'?> ".$this->OFXContent);

        $conf= explode('
', trim($this->conf));
        $this->conf= Array();

        for($i=0, $j= sizeof($conf); $i<$j; $i++)
        {
            $conf[$i]= explode(':', $conf[$i], 2);
            $this->conf[$conf[$i][0]]= $conf[$i][1];
        }
        $this->moviments= Array();

        foreach($this->ofx->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKTRANLIST->STMTTRN as $mov)
        {
            $this->moviments[]= (array)$mov;
        }

        $this->bank_id = (string)$this->ofx->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->BANKID;
        $this->branch_id = (string)$this->ofx->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->BRANCHID;
        $this->account_id = (string)$this->ofx->BANKMSGSRSV1->STMTTRNRS->STMTRS->BANKACCTFROM->ACCTID;
        $this->balance_amount = (string)$this->ofx->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->BALAMT;
        $this->balance_date = (string)$this->ofx->BANKMSGSRSV1->STMTTRNRS->STMTRS->LEDGERBAL->DTASOF;

        unset($this->originalString);
        unset($this->ofx);

        return $this->getIndexedById();
    }

    public function getBaseId()
    {
        return $this->bank_id.$this->branch_id.$this->account_id;
    }

    public function toCSV($resource)
    {
        if (is_string($resource)) {
            $resource = fopen($resource, "w");
        }
        foreach($this as $id => $mov) {
            $mov['id'] = $id;
            fputcsv($resource, $mov);
        }
        fclose($resource);
    }

    public function getIndexedById()
    {
        $movimentos = array();

        foreach($this->moviments as $i => $m) {
            $movimento = array(
                'data'  => substr($m['DTPOSTED'], 0, 8),
                'valor' => floatval($m['TRNAMT']),
                'desc'  => trim($m['MEMO']),
                'fitid' => $m['FITID'], // unique
                //'check' => $m['CHECKNUM'], // not unique
                //'type'  => $m['TRNTYPE'], // always 'OTHER'
            );
            $movimentos[] = $movimento;
        }

        return $movimentos;
    }

    /****************************************************************/
    /****             external functions to be used              ****/
    /****            (advised to be used for filters)            ****/
    /****************************************************************/

    public function filter($what, $forWhat, $case= true, $like= false)
    {
        $case= !$case;
        $ret= Array();
        if($case)
        {
            $what= strtoupper($what);
            $forWhat= strtoupper($forWhat);
        }
        for($i=0, $j= sizeof($this->moviments); $i<$j; $i++)
        {
            if($like)
            {
                if(strstr($this->moviments[$i][$what], $forWhat))
                    $ret[]= $this->moviments[$i];
            }else{
                    if($this->moviments[$i][$what] == $forWhat)
                        $ret[]= $this->moviments[$i];
                 }
        }
        return $ret;
    }

    public function getMoviments()
    {
        return $this->moviments;
    }

    public function getMemoLike($memo)
    {
        return $this->filter('MEMO', $memo, true, false);
    }

    public function getById($id)
    {
        return $this->filter('FITID', $id);
    }

    public function getByCheckNum($id)
    {
        return $this->filter('CHECKNUM', $id);
    }

    public function getCredits($min= 0)
    {
        $ret= Array();
        for($i=0, $j= sizeof($this->moviments); $i<$j; $i++)
        {
            if($this->moviments[$i]['TRNTYPE'] == 'CREDIT' && $this->moviments[$i]['TRNAMT'] >= $min)
                $ret[]= $this->moviments[$i];
        }
        return $ret;
    }
    public function getByDate($month, $day, $year)
    {
        if($month<10)
            $month= '0'.$month;
        if($day<10)
            $day= '0'.$day;
        if($year<100)
            if($year<10)
                $year= '00'.$year;
            else
                $year= '0'.$year;
        $date=  '/^'.$year.$month.$day . '([0-9])+$/';

        $ret= Array();
        for($i=0, $j= sizeof($this->moviments); $i<$j; $i++)
        {
            if(preg_match($date, $this->moviments[$i]['DTPOSTED']))
            {
                $ret[]= $this->moviments[$i];
            }
        }
        return $ret;
    }

    public function getMoviment($mov)
    {
        if(isset($this->moviments[$mov]))
            return $this->moviments[$mov];
        else
            return false;
    }

    public function getDebits($min= 0)
    {
        $ret= Array();

        if($min>0)
            $min*= (-1);

        for($i=0, $j= sizeof($this->moviments); $i<$j; $i++)
        {
            if($this->moviments[$i]['TRNTYPE'] == 'DEBIT' && $this->moviments[$i]['TRNAMT'] <= $min)
                $ret[]= $this->moviments[$i];
        }
        return $ret;
    }

    private function fixTag($tag, $acceptSpaces= false)
    {
        $tag= strtoupper($tag);
        $spaces = $acceptSpaces ? ' ': '';
        $this->OFXContent= preg_replace("!^<$tag>[^<]+$!mi", "\$0</$tag>", $this->OFXContent);
    }
}
