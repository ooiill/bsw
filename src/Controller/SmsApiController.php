<?php

namespace App\Controller;

use App\Module\Entity\Enum;
use Leon\BswBundle\Component\AwsSDK;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\BswCaptcha;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity as Error;
use Symfony\Component\HttpFoundation\Response;
use Respect\Validation\Validator as v;
use FOS\RestBundle\Controller\Annotations as Rest;
use Leon\BswBundle\Annotation\Entity\Input as I;
use Leon\BswBundle\Annotation\Entity\Output as O;

/**
 * Sms service
 */
class SmsApiController extends AcmeApiController
{
    /**
     * Validator for receiver
     *
     * @param string $receiver
     * @param array  $extra
     *
     * @return Response|true
     * @throws
     */
    protected function receiverValidator(string $receiver, array $extra)
    {
        switch ($extra['type']) {

            case Abs::CAPTCHA_SMS:
                !v::phone()->validate($receiver) && $error = 'Receiver must be valid phone number';
                break;

            case Abs::CAPTCHA_EMAIL:
                !v::email()->validate($receiver) && $error = 'Receiver must be valid email';
                break;
        }

        if (isset($error)) {
            return $this->failed(new Error\ErrorParameter(), $error);
        }

        return true;
    }

    /**
     * Sns config
     *
     * @param int $scene
     * @param int $type
     *
     * @return array
     */
    protected function snsConfig(int $scene, int $type): array
    {
        $sms = Abs::CAPTCHA_SMS;
        $mail = Abs::CAPTCHA_EMAIL;

        $commonConfig = [
            'signature' => $this->cnf->app_name,
            'expire'    => 600,
            'cooling'   => $this->debug ? 10 : 60,
            'type'      => [$sms => 'number', $mail => 'mixed'],
            'digit'     => [$sms => 4, $mail => 6],
        ];

        $mapConfig = [
            Abs::SNS_SCENE_SIGN_IN  => [ // Login
                'title'    => 'Sign in captcha',
                'message'  => [
                    $sms  => '[phone] Sign in captcha code『{code}』expires in {expire} minute',
                    $mail => '[email] Sign in captcha code『{code}』expires in {expire} minute',
                ],
                'tpl_code' => 'SMS_0001',
                'digit'    => 6,
            ],
            Abs::SNS_SCENE_SIGN_UP  => [ // Register
                'title'    => 'Sign up captcha',
                'message'  => [
                    $sms  => '[phone] Sign up captcha code『{code}』expires in {expire} minute',
                    $mail => '[email] Sign up captcha code『{code}』expires in {expire} minute',
                ],
                'tpl_code' => 'SMS_0002',
            ],
            Abs::SNS_SCENE_PASSWORD => [ // Forget password
                'title'    => 'Change password captcha',
                'message'  => [
                    $sms  => '[phone] Change password captcha code『{code}』expires in {expire} minute',
                    $mail => '[email] Change password captcha code『{code}』expires in {expire} minute',
                ],
                'tpl_code' => 'SMS_0003',
            ],
            Abs::SNS_SCENE_BIND     => [ // Bind
                'title'    => 'Tourist bind captcha',
                'message'  => [
                    $sms  => '[phone] Tourist bind captcha code『{code}』expires in {expire} minute',
                    $mail => '[email] Tourist bind captcha code『{code}』expires in {expire} minute',
                ],
                'tpl_code' => 'SMS_0004',
            ],
        ];

        $handling = [];
        $item = array_merge($commonConfig, $mapConfig[$scene] ?? []);
        foreach ($item as $key => $value) {
            if (is_array($value)) {
                $handling[$sms][$key] = $value[$sms] ?? null;
                $handling[$mail][$key] = $value[$mail] ?? null;
            } else {
                $handling[$sms][$key] = $value;
                $handling[$mail][$key] = $value;
            }
        }

        return $handling[$type] ?? [];
    }

    /**
     * @param array $config
     *
     * @return array
     */
    protected function snsArgs(array $config): array
    {
        // generate code
        $code = Helper::randString($config['digit'], $config['type']);
        $tpl = $this->translator->trans($config['message'], [], 'messages');

        $args = [
            'code'     => $code,
            'expire'   => intval($config['expire'] / Abs::TIME_MINUTE),
            'app'      => $this->cnf->app_name,
            'official' => $this->cnf->host_official,
        ];

        $keys = Helper::arrayMap(array_keys($args), '{%s}');
        $tpl = str_replace($keys, array_values($args), $tpl);

        return [$tpl, $code, $args];
    }

    /**
     * Send phone message
     *
     * @param string $receiver
     * @param string $tpl
     * @param array  $config
     * @param array  $args
     *
     * @return Response|bool
     * @throws
     */
    protected function sendPhoneMessage(string $receiver, string $tpl, array $config, array $args)
    {
        $platform = $this->parameter('platform_sms');

        switch ($platform) {
            case Abs::CLOUD_ALI:
                $result = $this->smsAli(86, $receiver, $config['signature'], $config['tpl_code'], $args);
                break;

            case Abs::CLOUD_TX:
                $result = $this->smsTx(86, $receiver, "【{$config['signature']}】{$tpl}");
                break;

            default:
                $result = $this->failed(new Error\ErrorSns());
                break;
        }

        return $result;
    }

    /**
     * Send email message
     *
     * @param string $receiver
     * @param string $title
     * @param string $content
     *
     * @return Response|bool
     * @throws
     */
    protected function sendEmailMessage(string $receiver, string $title, string $content)
    {
        $platform = $this->parameter('platform_email');

        switch ($platform) {
            case Abs::CLOUD_AWS:
                $awsEmail = $this->parameter('aws_email_class') ?: AwsSDK::class;
                $result = $this->emailAws($receiver, $title, $content, $awsEmail);
                break;

            case 'smtp':
                $result = $this->emailSMTP($receiver, $title, $content);
                break;

            case 'mail-gun':
                $result = $this->emailGun($receiver, $title, $content);
                break;

            default:
                $result = $this->failed(new Error\ErrorSns());
                break;
        }

        return $result;
    }

    /**
     * Create captcha and send
     *
     * @Rest\Post("/common/captcha", name="api_common_captcha")
     *
     * @I("type", rules={"unsInteger", "inKey": Enum::BSW_CAPTCHA_TYPE})
     * @I("receiver", validator=true)
     * @I("scene", rules={"unsInteger", "inKey": Enum::BSW_CAPTCHA_SCENE})
     *
     * @O("receiver")
     * @O("captcha", label="Development captcha")
     *
     * @return Response
     * @throws
     */
    public function postCaptchaAction(): Response
    {
        if (($args = $this->valid(Abs::V_SIGN)) instanceof Response) {
            return $args;
        }

        $config = $this->snsConfig($args->scene, $args->type);
        if (empty($config)) {
            return $this->failed(new Error\ErrorParameter(), 'Captcha map invalid');
        }

        $account = $this->account($args->receiver, true);

        // captcha record
        $bswCaptcha = $this->repo(BswCaptcha::class);
        $captcha = $bswCaptcha->findOneBy(
            [
                'type'    => $args->type,
                'account' => $account,
                'scene'   => $args->scene,
            ]
        );

        if ($captcha) {
            $diff = time() - strtotime($captcha->updateTime);
            // is cooling ?
            if ($captcha->expireTime > 0 && $diff < $config['cooling']) {
                return $this->failed(new Error\ErrorRequestOften());
            }
        }

        [$tpl, $code, $params] = $this->snsArgs($config);

        if ($args->type == Abs::CAPTCHA_SMS) {
            $result = $this->sendPhoneMessage($args->receiver, $tpl, $config, $params);
        } else {
            $result = $this->sendEmailMessage($args->receiver, $this->translator->trans($config['title']), $tpl);
        }

        if ($result instanceof Response) {
            return $result;
        }

        // newly record
        $bswCaptcha->newlyOrModify(
            [
                'type'    => $args->type,
                'account' => $account,
                'scene'   => $args->scene,
            ],
            [
                'captcha'    => $code,
                'expireTime' => time() + $config['expire'],
            ]
        );

        // response args
        $result = ['receiver' => $args->receiver];
        if ($this->debug) {
            $result['captcha'] = $code;
        }

        return $this->okay($result, 'Captcha send success');
    }

    /**
     * Verify captcha
     *
     * @Rest\Post("/common/captcha-checker", name="api_common_captcha_checker")
     *
     * @I("type", rules={"unsInteger", "inKey": Enum::BSW_CAPTCHA_TYPE})
     * @I("receiver", validator=true)
     * @I("scene", rules={"unsInteger", "inKey": Enum::BSW_CAPTCHA_SCENE})
     * @I("captcha", rules="inLength,4,6")
     *
     * @O("receiver")
     * @O("captcha", label="Development captcha")
     *
     * @return Response
     * @throws
     */
    public function postCheckCaptchaSmsAction(): Response
    {
        if (($args = $this->valid(Abs::V_SIGN)) instanceof Response) {
            return $args;
        }

        $args->receiver = $this->account($args->receiver, true);
        $valid = $this->checkCaptchaSms($args->type, $args->receiver, $args->captcha, $args->scene);
        if (!$valid) {
            return $this->failed(new Error\ErrorCaptcha());
        }

        return $this->success('Captcha is valid');
    }
}