<?php

namespace DYC\Pay\Gateways\Wechat;

use Symfony\Component\HttpFoundation\Request;
use DYC\Pay\Gateways\Wechat;
use DYC\Pay\Log;
use DYC\Supports\Collection;

class AuthGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return Collection
     */
    public function pay($endpoint, array $payload): Collection
    {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';

        $params = [
			'appid' => $payload['appid'],
			'redirect_uri' => urlencode($payload['redirect_uri']),
			'response_type' => 'code',
			'scope' => 'snsapi_base',
		];

        $params = http_build_query($params) . '#wechat_redirect	';
		$url .= $params;
		var_dump($url);die;


        $type = isset($payload['type']) ? ($payload['type'].($payload['type'] == 'app' ?: '_').'id') : 'app_id';

        $payload['mch_appid'] = $this->config->get($type, '');
        $payload['mchid'] = $payload['mch_id'];
        $payload['nonce_str'] = 'ED8J2kdcC9fbGhfH';
        php_sapi_name() === 'cli' ?: $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');

        unset($payload['appid'], $payload['mch_id'], $payload['trade_type'],
            $payload['notify_url'], $payload['type']);

		$payload['sign'] = Support::generateSign($payload, $this->config->get('key'));

        Log::debug('Paying A Transfer Order:', [$endpoint, $payload]);

        return Support::requestApi(
            'mmpaymkttransfers/promotion/transfers',
            $payload,
            $this->config->get('key'),
            ['cert' => $this->config->get('cert_client'), 'ssl_key' => $this->config->get('cert_key')]
        );
    }

    /**
     * Get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getTradeType(): string
    {
        return '';
    }
}
