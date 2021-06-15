<?php

namespace Template\Entity;

class Instructor
{
    public $id;
    public $firstname;
    public $lastname;

    public function __construct($id, $firstname, $lastname)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
}
