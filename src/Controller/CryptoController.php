<?php

namespace Drupal\crypto\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Class CryptoController.
 */
class CryptoController extends ControllerBase {

  /**
   * Getvalues.
   *
   * @return string
   *   Return Hello string.
   */
  public function getValues() {

    $json = file_get_contents('https://api.coindesk.com/v1/bpi/currentprice.json');
    $data = json_decode($json);

    $currentValueOfBtcInEuro = $data->bpi->EUR->rate_float;

    $nids = \Drupal::entityQuery('node')->condition('type','bitcoin_operation')->execute();

    $nodes = Node::loadMultiple($nids);

    foreach ($nodes as $node){
      $node = Node::load($node->id());

      if (isset($node->field_price)){
        $curprice = floatval($node->field_number->value) * $currentValueOfBtcInEuro;
        $node->set("title",$curprice);
        $node->set("field_price",$currentValueOfBtcInEuro);
        $node->save();
      }
    }

    $currdate =  date('l jS \of F Y h:i:s A');

    //USE REFERENCIAL
    return [
      "#title" => $currdate."  Current price of BTC ".$currentValueOfBtcInEuro,
    ];


  }

}
