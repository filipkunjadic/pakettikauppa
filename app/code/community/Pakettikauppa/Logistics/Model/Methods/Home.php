<?php
require_once(Mage::getBaseDir('lib') . '/pakettikauppa/autoload.php');
require_once(Mage::getBaseDir('lib') . '/pakettikauppa/Client.php');
class Pakettikauppa_Logistics_Model_Methods_Home
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'pakettikauppa_homedelivery';

  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
      /** @var Mage_Shipping_Model_Rate_Result $result */
      $result = Mage::getModel('shipping/rate_result');
      $client = new Pakettikauppa\Client(array('test_mode' => true));
      $methods = json_decode($client->listShippingMethods());
      foreach($methods as $method){
        $result->append($this->_getCustomRate($method->name,$method->shipping_method_code, 999));
      }
      return $result;

  }
  /**
   * Returns Allowed shipping methods
   *
   * @return array
   */
  public function getAllowedMethods()
  {
    $client = new Pakettikauppa\Client(array('test_mode' => true));
    $methods = json_decode($client->listShippingMethods());
    foreach($methods as $carrier){
      $array['shipping_'.$carrier->shipping_method_code] = $carrier->name;
    }
    return $array;
  }


  protected function _getCustomRate($name, $method_code, $price)
  {
      /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
      $rate = Mage::getModel('shipping/rate_result_method');
      $rate->setCarrier($this->_code);
      $rate->setCarrierTitle($this->getConfigData('title'));
      $rate->setMethod($method_code);
      $rate->setMethodTitle($name);
      $rate->setPrice($price);
      $rate->setCost(0);
      return $rate;
  }

  public function isTrackingAvailable()
  {
      return true;
  }

  public function getTrackingInfo($tracking)
  {
    $track = Mage::getModel('shipping/tracking_result_status');
    $track->setUrl('http://pakettikauppa.local/pakettikauppalogistics/tracking?t='.$tracking)
            ->setTracking($tracking)
            ->setCarrierTitle('Posti');
    return $track;
  }
}