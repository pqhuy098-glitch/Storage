<?php
#[\AllowDynamicProperties]

class HotelUser {
    public $fullname;
    public $email;
    public $password;
    public $role;

    public function __construct($fullname, $email, $password, $role) {
        $this -> fullname = $fullname;
        $this -> email = $email;
        $this -> password = $password;
        $this -> role = $role;
    }
}