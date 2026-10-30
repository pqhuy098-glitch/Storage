<?php
#[\AllowDynamicProperties]

class Customer extends HotelUser {
    public $customerID;
    public $nationalID;
    public $phoneNumber;

    public function __construct($fullname, $email, $password, $role, $customerID, $nationalID, $phoneNumber) {
        parent::__construct($fullname, $email, $password, $role);
        $this -> customerID = $customerID;
        $this -> nationalID = $nationalID;
        $this -> phoneNumber = $phoneNumber;
    }
}