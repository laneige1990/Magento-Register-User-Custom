<?php
class Laneonline_Registeruser_IndexController extends Mage_Core_Controller_Front_Action {        
    public function indexAction() {
        // Email is validated on the front end but we'll validate the email 
        //backend just in case user trys anything funny.   
        $email = $_POST['email'];
        $email_confirm = $_POST['email_confirm'];
        $password = $_POST['password'];
        $first_name = $_POST['firstname'];
        $middle_name = $_POST['middlename'];
        $last_name = $_POST['lastname'];
        
        $customer_array = array(
            "email" => $email,
            "password" => $password,
            "first_name" => $first_name,
            "middle_name" => $middle_name,
            "last_name" => $last_name
        );
        
        if (!$this->validateEmail($email)){
            return;
        }
        
        // next, is to check if the email address is no already on the system
        if (!$this->checkEmailExists($email)){
            return;
        }
        
        // check to see if email matches second email
        if (!$this->checkEmailMatches($email, $email_confirm)){
            return;
        }
        
        // if all validated, now add user to system
        if (!$this->addCustomer($customer_array)){
            return;
        }
    }
    
    public function addCustomer($customer_array){
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($customer_array["email"]);
        
        if(!$customer->getId()) {
            $customer->setEmail($customer_array["email"]);
            $customer->setFirstname($customer_array["first_name"]);
            $customer->setMiddlename($customer_array["middle_name"]);
            $customer->setLastname($customer_array["last_name"]);
            $customer->setPassword($customer_array["password"]);
        }
        try {
            $customer->save();
            $customer->setConfirmation(null);
            $customer->save();

            // Send welcome email
            $storeId = $customer->getSendemailStoreId();
            $customer->sendNewAccountEmail('registered', '', $storeId);
            //Make a "login" of new customer
            Mage::getSingleton('customer/session')->loginById($customer->getId());
 
            Mage::getSingleton('customer/session')->addSuccess(__('Thank you, you have been added to our system!'));
            $this->_redirect('/');

        }
        catch (Exception $ex) {
           Mage::getSingleton('customer/session')->addError(__('Sorry, but there was an error creating your account. Please try again, or contact support.'));
            $this->_redirect('customer/account/create/');

            return false;
        }
        
    }
    
    public function validateEmail($email){
        try {
            
             $error = false;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
               $error = true;
            }

            if ($error) {
                throw new Exception();
            }

        
        } catch (Exception $e) {
            
            Mage::getSingleton('customer/session')->addError(__('Sorry, your email is not a valid format.'));
            $this->_redirect('customer/account/create/');

             return false;
        }
        
        return true;
    }
    
    public function checkEmailExists($email){
         $users = mage::getModel('customer/customer')->getCollection()
               ->addAttributeToSelect('email');

        foreach ($users as $user){
           try {
           if ($email == $user->getData()['email']){
               $error = true;
            }

            if ($error) {
                throw new Exception();
            }

        } catch (Exception $e) {
            
            Mage::getSingleton('customer/session')->addError(__('Sorry, but you already have an account registered with us.'));
            $this->_redirect('customer/account/create/');

            return false;
        }  

        }
         return true;
    }
    
    public function checkEmailMatches($email,$email_confirm){
           try {
            
             $error = false;

            if ($email != $email_confirm) {
               $error = true;
            }

            if ($error) {
                throw new Exception();
            }

        } catch (Exception $e) {
            
            Mage::getSingleton('customer/session')->addError(__('Sorry, your emails do not match.'));
            $this->_redirect('customer/account/create/');

             return false;
        }
        return true;
    }
    
    
        
}