<?php

namespace App\Component;

use App\Module\Entity\Abs;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\Service as BswService;
use Leon\BswBundle\Module\Exception\ServiceException;
use Leon\BswBundle\Module\Traits\Message;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Service extends BswService
{
    use Message;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var array
     */
    protected $headerArgs = [];

    /**
     * @var array
     */
    protected $nonSignArgs;

    /**
     * @var string
     */
    protected $keyToken = 'token';

    /**
     * @var string
     */
    protected $keyTime = 'time';

    /**
     * @var string
     */
    protected $keySign = 'signature';

    /**
     * Service constructor.
     *
     * @param string $host
     * @param string $salt
     * @param int    $timeout
     * @param array  $header
     */
    public function __construct(string $host, string $salt, int $timeout, array $header = [])
    {
        $this->host($host);
        $this->salt = $salt;
        $this->timeout($timeout);
        $this->headerArgs = $header;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function setKeyToken(string $key)
    {
        return $this->keyToken = $key;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function setKeyTime(string $key)
    {
        return $this->keyTime = $key;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function setKeySign(string $key)
    {
        return $this->keySign = $key;
    }

    /**
     * @param array $argKeys
     *
     * @return Service
     */
    public function nonSignArgs(array $argKeys = []): Service
    {
        $this->nonSignArgs = $argKeys;

        return $this;
    }

    /**
     * Call api by DELETE
     *
     * @param string $api
     * @param array  $params
     * @param string $token
     *
     * @return array|false
     * @throws
     */
    public function delete(string $api, array $params = [], string $token = null)
    {
        $this->method(Abs::REQ_DELETE);

        return $this->get($api, $params, $token);
    }

    /**
     * Call api by POST
     *
     * @param string $api
     * @param array  $params
     * @param string $token
     *
     * @return array|false
     * @throws
     */
    public function post(string $api, array $params = [], string $token = null)
    {
        $this->method(Abs::REQ_POST);

        return $this->get($api, $params, $token);
    }

    /**
     * Call api by GET
     *
     * @param string $api
     * @param array  $params
     * @param string $token
     *
     * @return array|false
     * @throws
     */
    public function get(string $api, array $params = [], string $token = null)
    {
        $this->path($api);
        $this->args($params);

        $args = $this->args;
        if ($this->nonSignArgs) {
            Helper::arrayPop($args, $this->nonSignArgs);
        }

        if ($token) {
            $this->headerArgs[$this->keyToken] = $token;
        }

        $header = Helper::createSignature($args, $this->salt, false, $this->keyTime, $this->keySign);
        $header = Helper::arrayPull($header, [$this->keyTime, $this->keySign]);
        $header = array_merge($header, $this->headerArgs);
        $this->header($header);

        try {
            $result = $this->request();
        } catch (ServiceException $e) {
            exit("Service Output =>> {$e->getMessage()}");
        }

        if ($result['code'] != HttpResponse::HTTP_OK) {
            throw new ServiceException("Service Exception =>> {$result['message']}", $result['error'], $result['code']);
        }

        if ($result['error']) {
            return $this->push($result['message'], $result['error']);
        }

        return $result['sets'];
    }
}