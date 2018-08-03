<?php

namespace DYC\Pay\Gateways\Alipay;

use DYC\Pay\Contracts\GatewayInterface;
use DYC\Pay\Log;
use DYC\Supports\Collection;
use DYC\Supports\Config;

class AuthGateway implements GatewayInterface
{
    /**
     * Config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Bootstrap.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

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
    public function pay($endpoint, array $payload)
    {
    	$params = [];
		$params['app_id']     = $this->config->get('app_id');
		$params['apiname']    = "com.alipay.account.auth";
		$params['app_name']   = "mc";
		$params['biz_type']   = "openservice";
		$params['pid']        = $this->config->get('pid');
		$params['product_id'] = "APP_FAST_LOGIN";
		$params['scope']      = "kuaijie";
		$params['target_id']  = time();
		$params['auth_type']  = "AUTHACCOUNT";
		$params['sign_type']  = 'RSA2';//商户生成签名字符串所使用的签名算法类型
        Log::debug('生成签名 :', $params);
		$paramStr = Support::getSignContent($params);
		$params['sign'] = Support::alonersaSign($paramStr, $this->config->get('private_key'));
        return $this->getSignContentUrlencode($params);
    }

	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	public function characet($data, $targetCharset) {

		if (!empty($data)) {
			$fileType = 'UTF-8';
			if (strcasecmp($fileType, $targetCharset) != 0) {
				$data = mb_convert_encoding($data, $targetCharset, $fileType);
				//				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
			}
		}

		return $data;
	}

	//此方法对value做urlencode
	public function getSignContentUrlencode($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				// 转换成目标字符集
				$v = $this->characet($v, 'UTF-8');

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . urlencode($v);
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
				}
				$i++;
			}
		}

		unset ($k, $v);
		return $stringToBeSigned;
	}

    /**
     * Get method config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getMethod(): string
    {
        return 'alipay.fund.trans.toaccount.transfer';
    }

    /**
     * Get productCode config.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    protected function getProductCode(): string
    {
        return '';
    }
}
