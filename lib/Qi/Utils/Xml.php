<?php

namespace Qi\Utils;

class Xml
{
  /**
   * Pretty Print XML
   */
  public static function pp($xml)
  {
    if ($xml instanceof \SimpleXMLElement) $xml = $xml->asXML();
    $dom = new \DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml);
    return $dom->saveXML($dom->documentElement);
  }
}
