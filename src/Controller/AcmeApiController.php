<?php

namespace App\Controller;

use App\Module\Entity\Abs;
use App\Module\Entity\Enum;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Component\JWT;
use Leon\BswBundle\Controller\BswApiController;
use Leon\BswBundle\Entity\BswCaptcha;
use Leon\BswBundle\Module\Error\Entity\ErrorAuthorization;
use Leon\BswBundle\Module\Error\Error;
use Leon\BswBundle\Module\Hook\Entity\Aes as AesHook;
use Leon\BswBundle\Component\Aes;
use Leon\BswBundle\Module\Hook\Entity\Enums;
use Leon\BswBundle\Repository\BswCaptchaRepository;
use Exception;

class AcmeApiController extends BswApiController
{
    /**
     * @var array
     */
    public $acmeController = [AcmeApiController::class];

    /**
     * @var bool
     */
    protected $plaintextSensitive = true;

    /**
     * @var bool
     */
    protected $responseEncrypt = false;

    /**
     * @var array
     */
    protected $headerMap = [
        'time',
        'signature'     => 'sign',
        'lang',
        'token',
        'os',
        'ua',
        'device',
        'channel',
        'version-code'  => 'version',
        'sign-dynamic',
        'sign-close',
        'sign-debug',
        'postman-token' => 'postman',
    ];

    /**
     * Bootstrap
     */
    protected function bootstrap()
    {
        parent::bootstrap();

        $this->header->os = array_flip(Enum::DEVICE_OS)[$this->header->os] ?? 0;
        $this->header->ua = Helper::parseJsonString($this->header->ua, []);

        if (empty($this->header->version)) {
            $this->header->version = 0;
        }

        $this->header->channel = intval($this->header->channel);
        if ($this->header->channel < 0) {
            $this->header->channel = 0;
        }
    }

    /**
     * Get old sign
     *
     * @param array $args
     *
     * @return string
     */
    protected function apiOldSign(array $args): string
    {
        return $this->header->sign;
    }

    /**
     * Get new sign
     *
     * @param array $args
     * @param bool  $debug
     *
     * @return string
     */
    protected function apiNewSign(array $args, bool $debug = false): string
    {
        $diff = $this->cnf->api_vs_client_time_diff;
        $now = time();
        $time = $this->header->time ?? $now;

        if (!$this->debug && $diff && abs($now - $time) > $diff) {
            $this->logger->debug("Client time difference {$diff} second with server");
            $this->logger->debug("Client time is {$time} and server is {$now}");

            return Helper::generateToken();
        }

        $args['time'] = $time;
        $sign = Helper::createSignature($args, $this->parameter('salt'), $debug);
        $this->logger->warning("The request signature, {$sign['signature']}");

        return $sign['signature'];
    }

    /**
     * Should authorization
     *
     * @param array $args
     *
     * @return array|object|Error
     */
    protected function apiShouldAuth(array $args)
    {
        /**
         * @var JWT $jwt
         */
        $jwt = $this->component(JWT::class);

        if (empty($this->header->token)) {
            return new ErrorAuthorization();
        }

        try {
            $session = $jwt->parse($this->header->token);
        } catch (Exception $e) {
            $this->logger->error('JWT parse failed', [$e->getMessage()]);
        }

        if (empty($session)) {
            return new ErrorAuthorization();
        }

        Helper::arrayPop($session, ['iss', 'iat', 'exp']);

        return $session;
    }

    /**
     * 用户会话信息
     *
     * @param int    $userId
     * @param string $message
     * @param bool   $forResponse
     *
     * @return array
     * @throws
     */
    protected function userSession(int $userId, string $message = null, bool $forResponse = true): array
    {
        // TODO
        return [];
    }

    /**
     * Strict login
     *
     * @return bool|Error
     */
    protected function strictAuthorization()
    {
        if (!$this->usr) {
            return false;
        }

        $session = $this->userSession($this->usr->user_id, null, false);

        // when record modify
        if (($this->usr->update_time ?? null) != $session['update_time']) {

            $strict = ['salt', 'password', 'last_login'];
            foreach ($strict as $field) {
                $value = $this->usr->{$field} ?? time();
                if ($value != ($session[$field] ?? false)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Handler content before response
     *
     * @param array $response
     *
     * @return array|string
     */
    protected function beforeResponse(array $response)
    {
        if ($this->signDynamic || $this->development) {
            return $response;
        }

        /**
         * @var Aes $aes
         */
        $aes = $this->component(Aes::class);
        $plaintext = json_encode($response, JSON_UNESCAPED_UNICODE);

        return $aes->AESEncode($plaintext);
    }

    /**
     * Difference type
     *
     * @param int $type
     *
     * @return int
     */
    protected function diffType(int $type): int
    {
        if ($fromH5 = empty($this->header->sign)) {
            /**
             * Request without signature
             */
            if (Helper::bitFlagAssert($type, Abs::V_SIGN)) {
                $type = $type ^ Abs::V_SIGN;
            }

            /**
             * Response without encrypt
             */
            $this->responseEncrypt = false;
        }

        return $type;
    }

    /**
     * @return array
     */
    protected function hookerExtraArgs(): array
    {
        return [
            AesHook::class => [
                'aes_iv'    => $this->parameter('aes_iv'),
                'aes_key'   => $this->parameter('aes_key'),
                'plaintext' => $this->plaintextSensitive,
            ],
            Enums::class   => [
                'trans' => $this->translator,
            ],
        ];
    }

    /**
     * @param array $cnf
     *
     * @return array
     */
    protected function extraConfig(array $cnf): array
    {
        $apiConfig = $this->parameter('api_cnf', [], false);
        $dbConfig = $this->getDbConfig('api_database_config');

        $pair = array_merge($cnf, $apiConfig, $dbConfig);
        $pair = Helper::numericValues($pair);

        return $pair;
    }

    /**
     * Encode/Decode account
     *
     * @param string $account
     * @param bool   $persistence
     *
     * @return string
     */
    protected function account(string $account, bool $persistence = false): string
    {
        $data = ['account' => $account];
        $data = $this->hooker([AesHook::class => ['account']], $data, $persistence);

        return current($data);
    }

    /**
     * Check sms captcha
     *
     * @param int    $type
     * @param string $account
     * @param string $captcha
     * @param int    $scene
     * @param bool   $once
     *
     * @return bool
     * @throws
     */
    protected function checkCaptchaSms(
        int $type,
        string $account,
        string $captcha,
        int $scene,
        bool $once = true
    ): bool {
        /**
         * @var BswCaptchaRepository $bswCaptcha
         */
        $bswCaptcha = $this->repo(BswCaptcha::class);
        $record = $bswCaptcha->findOneBy(
            [
                'type'    => $type,
                'account' => $account,
                'captcha' => $captcha,
                'scene'   => $scene,
                'state'   => Abs::NORMAL,
            ]
        );

        if (empty($record) || $record->expireTime < time()) {
            return false;
        }

        // invalid captcha
        if ($once) {
            $bswCaptcha->modify(['id' => $record->id], ['expireTime' => 0]);
        }

        return true;
    }
}