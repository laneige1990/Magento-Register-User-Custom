<?php
/**
 * Our class name should follow the directory structure of
 * our Observer.php model, starting from the namespace,
 * replacing directory separators with underscores.
 * i.e. app/code/local/SmashingMagazine/
 *                     LogProductUpdate/Model/Observer.php
 */
class laneonline_registeruser_ModelObserver
{
    /**
     * Magento passes a Varien_Event_Observer object as
     * the first parameter of dispatched events.
     */
    public function RegisterUser(Varien_Event_Observer $observer)
    {
        $collection = mage::getModel('customer/customer')->getCollection();
    var_dump((string)$collection->getSelect());
        die();
    }
}