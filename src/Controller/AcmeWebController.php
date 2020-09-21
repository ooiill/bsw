<?php

namespace App\Controller;

use App\Component\Service;
use Leon\BswBundle\Controller\BswFrontendController;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Error;

class AcmeWebController extends BswFrontendController
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * bootstrap
     */
    public function bootstrap()
    {
        parent::bootstrap();

        /*
        $this->service = new Service(
            $this->cnf->host_service,
            $this->parameter('salt_service', null, false),
            $this->cnf->curl_timeout_second * 1000,
            [
                'lang' => $this->header->lang,
                'os'   => Abs::OS_WEB,
            ]
        );
        */
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error
     */
    protected function webShouldAuth(array $args)
    {
        return [];
    }
}