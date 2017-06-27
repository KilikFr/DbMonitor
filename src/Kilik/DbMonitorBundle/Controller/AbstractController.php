<?php

namespace Kilik\DbMonitorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    /**
     * @param string $manager
     *
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    public function manager($manager = 'default')
    {
        return $this->get('doctrine')->getManager($manager);
    }
}
