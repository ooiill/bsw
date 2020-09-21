<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;
use Leon\BswBundle\Controller\BswMixed\Upload;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity as Error;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mixed controller
 */
class MixedApiController extends AcmeApiController
{
    use Upload;

    /**
     * Welcome
     *
     * @Rest\Get("/", name="api_welcome")
     *
     * @O("date")
     * @O("version")
     *
     * @return Response
     */
    public function getWelcomeAction()
    {
        $type = $this->diffType(Abs::V_NOTHING);
        if (($args = $this->valid($type)) instanceof Response) {
            return $args;
        }

        return $this->okay(
            [
                'date'    => date(Abs::FMT_FULL),
                'version' => $this->parameter('version'),
            ],
            'Great!'
        );
    }

    /**
     * Clean cache
     *
     * @Rest\Get("/cache/clean", name="api_cache_clean")
     *
     * @I("dynamic_key")
     * @I("pop_cache_keys",rules="~|stringToArray")
     *
     * @return Response
     * @throws
     */
    public function getCleanCacheAction()
    {
        $type = $this->diffType(Abs::V_NOTHING);
        if (($args = $this->valid($type)) instanceof Response) {
            return $args;
        }

        list($dynamic) = $this->TOTPToken('cache');
        if ($args->dynamic_key !== md5($dynamic)) {
            return $this->failed(new Error\ErrorParameter(), 'Dynamic key error');
        }

        if (empty($args->pop_cache_keys)) {
            $result = $this->disCache();
        } else {
            $result = $this->popCache($args->pop_cache_keys);
        }

        if (!$result) {
            return $this->failed(new Error\ErrorUnknown(), 'Cache clear failed');
        }

        return $this->success('Cache clear success');
    }

    /**
     * Get dynamic key
     *
     * @Rest\Get("/dynamic", name="api_dynamic")
     *
     * @I("devil_key")
     * @I("uid")
     *
     * @O("dynamic_key")
     * @O("treaty_period")
     *
     * @return Response
     * @throws
     */
    public function getDynamicAction()
    {
        $type = $this->diffType(Abs::V_NOTHING);
        if (($args = $this->valid($type)) instanceof Response) {
            return $args;
        }

        if ($args->devil_key !== md5($this->cnf->debug_devil)) {
            return $this->failed(new Error\ErrorParameter(), 'Devil key error');
        }

        list($dynamic, $from, $to) = $this->TOTPToken($args->uid);

        return $this->okay(
            [
                'dynamic_key'   => md5($dynamic),
                'treaty_period' => "{$from} ~ {$to}",
            ]
        );
    }

    /**
     * Upload options handler
     *
     * @param string $flag
     * @param array  $options
     *
     * @return array
     */
    public function uploadOptionsHandler(string $flag, array $options): array
    {
        return $options;
    }
}