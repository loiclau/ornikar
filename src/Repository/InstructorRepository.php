<?php

namespace Template\Repository;

use Template\Entity\Instructor;
use Faker;

class InstructorRepository implements Repository
{
    use \Template\Helper\SingletonTrait;

    private $firstname;
    private $lastname;

    /**
     * InstructorRepository constructor.
     */
    public function __construct()
    {
        $this->firstname = Faker\Factory::create()->firstName;
        $this->lastname = Faker\Factory::create()->lastName;
    }

    /**
     * @param int $id
     *
     * @return Instructor
     */
    public function getById($id)
    {
        // DO NOT MODIFY THIS METHOD
        return new Instructor(
            $id,
            $this->firstname,
            $this->lastname
        );
    }
}
