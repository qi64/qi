<?php

namespace Qi\Utils;
use SimpleXMLElement, DOMDocument;

class Xml
{
  /**
  * Pretty Print XML string or SimpleXmlElement
  */
  public static function pp($xml)
  {
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    if ($xml instanceof SimpleXMLElement) {
      $dom_sxml = dom_import_simplexml($xml);
      $dom_sxml = $dom->importNode($dom_sxml, true);
      $dom->appendChild($dom_sxml);
    }else{
      $dom->loadXML((string)$xml);
    }
    return $dom->saveXML($dom->documentElement);
  }

  /**
   *  Lê um html ou xml mal formatado, utilizando loadXML que ignora a maioria dos erros.
   */
  public static function html2sxml($html, $removeHtmlBody = true)
  {
    $doc = new DOMDocument();
    $doc->strictErrorChecking = false;
    $doc->preserveWhiteSpace = false;
    $doc->loadHTML($html); // creates <html><body> </body></html>
    $sxml = simplexml_import_dom($doc);
    if ($removeHtmlBody) {
      $children = $sxml->body->children();
      return reset($children);
    }else{
      return $sxml;
    }
  }
}
