<?php

namespace Template\Repository;

use Faker;
use Template\Entity\MeetingPoint;

class MeetingPointRepository implements Repository
{
    use \Template\Helper\SingletonTrait;

    private $url;
    private $name;

    /**
     * SiteRepository constructor.
     *
     */
    public function __construct()
    {
        // DO NOT MODIFY THIS METHOD
        $this->url = Faker\Factory::create()->url;
        $this->name = Faker\Factory::create()->city;
    }

    /**
     * @param int $id
     *
     * @return MeetingPoint
     */
    public function getById($id)
    {
        // DO NOT MODIFY THIS METHOD
        return new MeetingPoint($id, $this->url, $this->name);
    }
}
