<?php

namespace Simplephp\IapService\Util;

use Exception;
use phpseclib3\File\X509;
use Simplephp\IapService\Exception\VerificationException;

class JWT
{
    /**
     * @var string
     */
    const TYPE = 'JWT';

    /**
     * @var string
     */
    const HEADER_TYP = 'typ';
    /**
     * @var string
     */
    const HEADER_ALG = 'alg';
    /**
     * @var string
     */
    const HEADER_KID = 'kid';
    /**
     * @var string
     */
    const HEADER_X5C = 'x5c';
    /**
     * (issuer)：签发人
     * @var string
     */
    const PAYLOAD_ISS = 'iss';
    /**
     *  (expiration time)：过期时间
     * @var string
     */
    const PAYLOAD_EXP = 'exp';
    /**
     *  (subject)：主题
     * @var string
     */
    const PAYLOAD_SUB = 'sub';
    /**
     * (audience)：受众
     * @var string
     */
    const PAYLOAD_AUD = 'aud';

    /**
     * (not before)：生效时间
     * @var string
     */
    const PAYLOAD_NBF = 'nbf';

    /**
     * (Issued At)：签发时间
     * @var string
     */
    const PAYLOAD_IAT = 'iat';

    /**
     * (JWT ID)：编号
     * @var string
     */
    const PAYLOAD_JTI = 'jti';

    /**
     * ES256
     * @var array
     */
    const ALGORITHM_ES256 = [
        'name' => 'ES256',
        'opensslAlgorithm' => OPENSSL_ALGO_SHA256,
        'hashAlgorithm' => 'sha256',
    ];

    /**
     * 默认配置
     * @var array $defaulOptions
     */
    private $defaultOptions = [
        'unsafeMode' => false,
        'algorithm' => self::ALGORITHM_ES256['name'],
        'leafCertOid' => '',
    ];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->defaultOptions = array_replace($this->defaultOptions, $options);
    }

    /**
     * @param $jwt
     * @param array $options
     * @return mixed
     * @throws VerificationException
     */
    public function decodedSignedData($jwt, array $options = [])
    {
        $options = array_replace($this->defaultOptions, $options);
        $parts = explode('.', $jwt);
        $partsCount = count($parts);
        if ($partsCount !== 3) {
            throw new VerificationException('jwt data format exception');
        }
        [$headersJson, $payloadJson, $signature] = array_map([Helper::class, 'base64Decode'], $parts);
        if (!$headersJson || !$payloadJson || !$signature) {
            throw new VerificationException('jwt data incomplete');
        }
        $headers = json_decode($headersJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new VerificationException('Failed to parse jwt header data');
        }
        $payload = json_decode($payloadJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new VerificationException('Failed to parse jwt payload data');
        }
        if (true == $options['unsafeMode']) {
            return $payload;
        }
        if (!isset($headers['x5c']) || !isset($headers['alg'])) {
            throw new VerificationException('jwt data missing x5c or alg data');
        }
        if ($headers['alg'] !== $options['algorithm']) {
            throw new VerificationException('mismatch of algorithms');
        }
        $x5c = $headers['x5c'] ?? [];
        list($leafCertPEM, $middleCertPEM, $rootCertPEM) = array_map([Helper::class, 'formatPEM'], $x5c);
        $this->verifyX509Chain($leafCertPEM, $middleCertPEM, $rootCertPEM);
        // skip:证书吊销检测，涉及二次网络请求，会有网络异常风险导致校验失败的问题
        //$this->verifyCRL4X509Chain([$leafCertPEM, $middleCertPEM]);
        $this->verifyOID($leafCertPEM, $options['leafCertOid']);
        $signedPart = substr($jwt, 0, strrpos($jwt, '.'));
        $this->verifySignature($headers['x5c'][0], $signedPart, $signature);
        return $payload;
    }

    /**
     * 数据签名校验
     * @param string $x5c0
     * @param string $input
     * @param string $signature
     * @throws VerificationException
     */
    private function verifySignature(string $x5c0, string $input, string $signature): void
    {
        try {
            $signatureAsASN1 = ASN1SequenceOfInteger::fromHex(bin2hex($signature));
            $publicKey = Helper::formatPEM($x5c0);
            if (openssl_verify($input, $signatureAsASN1, $publicKey, 'sha256') !== 1) {
                throw new VerificationException('Signature mismatch of data');
            }
        } catch (\Exception $e) {
            throw new VerificationException('Signature verification failed');
        }
    }

    /**
     * 校验X509证书链
     * @param string $leafCertPEM
     * @param string $middleCertPEM
     * @param string $rootCertPEM
     * @param string|null $rootCertificate
     * @throws VerificationException
     */
    public function verifyX509Chain($leafCertPEM, $middleCertPEM, $rootCertPEM, $rootCertificate = null)
    {
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            $this->verifyX509ChainV2($leafCertPEM, $middleCertPEM, $rootCertPEM, $rootCertificate);
        } else {
            $this->verifyX509ChainV1($leafCertPEM, $middleCertPEM, $rootCertPEM, $rootCertificate);
        }
    }

    /**
     * 验证x509证书数字签名 php >=7.4
     * @param string $leafCertPEM
     * @param string $middleCertPEM
     * @param string $rootCertPEM
     * @param string|null $rootCertificate
     * @throws VerificationException
     */
    protected function verifyX509ChainV2($leafCertPEM, $middleCertPEM, $rootCertPEM, $rootCertificate = null)
    {
        if (openssl_x509_verify($leafCertPEM, $middleCertPEM) !== 1) {
            throw new VerificationException('leaf certificate verification failed');
        }
        if (openssl_x509_verify($middleCertPEM, $rootCertPEM) !== 1) {
            throw new VerificationException('intermediate certificate verification failed');
        }
        if (!is_null($rootCertificate) && openssl_x509_verify($rootCertPEM, Helper::formatPEM($rootCertificate)) !== 1) {
            throw new VerificationException('root certificate verification failed');
        }
    }

    /**
     * 验证x509证书数字签名  php <7.4
     * @param string $leafCertPEM
     * @param string $middleCertPEM
     * @param string $rootCertPEM
     * @param string|null $rootCertificate
     * @throws VerificationException
     */
    protected function verifyX509ChainV1($leafCertPEM, $middleCertPEM, $rootCertPEM, $rootCertificate = null)
    {
        // 将证书加载为资源
        $leafCertResource = openssl_x509_read($leafCertPEM);
        $intermediateCertResource = openssl_x509_read($middleCertPEM);
        $rootCertResource = openssl_x509_read($rootCertPEM);
        if (!$leafCertResource || !$intermediateCertResource || !$rootCertResource) {
            throw new VerificationException('Failed to read certificates.');
        }
        // 创建临时文件来存储中间证书和根证书
        $intermediateCertFile = tempnam(sys_get_temp_dir(), 'intermediate');
        $rootCertFile = tempnam(sys_get_temp_dir(), 'root');
        file_put_contents($intermediateCertFile, $middleCertPEM);
        file_put_contents($rootCertFile, $rootCertPEM);
        // 使用 openssl_x509_checkpurpose 检查证书链
        $purpose = X509_PURPOSE_ANY;
        try {
            if (!openssl_x509_checkpurpose($leafCertResource, $purpose, [$intermediateCertFile, $rootCertFile])) {
                throw new VerificationException('Leaf certificate validation failed.');
            }
            if (!openssl_x509_checkpurpose($intermediateCertResource, $purpose, [$rootCertFile])) {
                throw new VerificationException('Intermediate certificate validation failed.');
            }
            if (!is_null($rootCertificate) && !openssl_x509_checkpurpose($intermediateCertResource, $purpose, [$rootCertificate])) {
                throw new VerificationException('root certificate verification failed');
            }
        } finally {
            unlink($intermediateCertFile);
            unlink($rootCertFile);
            is_resource($leafCertResource) && openssl_x509_free($leafCertResource);
            is_resource($intermediateCertResource) && openssl_x509_free($intermediateCertResource);
            is_resource($rootCertResource) && openssl_x509_free($rootCertResource);
        }
    }

    /**
     * skip : 涉及二次网络请求，暂时不使用
     * 验证一组 X509 证书链是否被吊销
     * OCSP 是一种用于验证证书状态的协议，它可以用来检查证书是否被吊销
     * @link https://www.cnxct.com/browsers-and-certificate-validation/
     * @link https://github.com/mlocati/ocsp/tree/master?tab=readme-ov-file#checking-if-a-certificate-has-been-revoked
     * @param array $x509PEMs 包含多个 X.509 证书的 PEM 格式字符串数组
     * @throws Exception
     */
    public function verifyCRL4X509Chain(array $x509PEMs)
    {
        foreach ($x509PEMs as $x509PEM) {
            // 初始化 X.509 操作对象
            $cert = new X509();
            try {
                // 尝试加载一个 X.509 证书
                if (!$cert->loadX509($x509PEM)) {
                    throw new VerificationException('Failed to load certificate.');
                }
                // 获取 CRL 分发点扩展信息
                $crlExt = $cert->getExtension("id-ce-cRLDistributionPoints");
                if (empty($crlExt) || empty($crlExt[0]["distributionPoint"]["fullName"][0]["uniformResourceIdentifier"])) {
                    throw new VerificationException('CRL distribution point not found.');
                }
                $crl_url = $crlExt[0]["distributionPoint"]["fullName"][0]["uniformResourceIdentifier"];

                // 使用 cURL 请求获取 CRL 数据
                $crl_data = $this->fetchCRL($crl_url);
                if (!$crl_data) {
                    throw new VerificationException('Failed to fetch CRL.');
                }

                // 加载并解析 CRL 数据
                $crl = new X509();
                if (!$crl_certs = $crl->loadCRL($crl_data)) {
                    throw new VerificationException('Failed to parse CRL.');
                }

                // 从 CRL 中获取被吊销的证书列表
                $revokedCerts = $crl_certs["tbsCertList"]["revokedCertificates"] ?? [];

                // 获取当前证书的序列号
                $current_cert = $cert->getCurrentCert();
                $current_sn = $current_cert["tbsCertificate"]["serialNumber"]->toString();

                // 检查当前证书是否在吊销列表中
                foreach ($revokedCerts as $crl_cert) {
                    if ($crl_cert['userCertificate']->toString() === $current_sn) {
                        throw new VerificationException('The certificate has been revoked.');
                    }
                }
            } catch (\Exception $e) {
                throw new VerificationException("Error processing certificate: " . $e->getMessage());
            }
        }
    }

    /**
     * 使用 cURL 获取 CRL 数据
     * @param string $url CRL 的 URL
     * @return string|false CRL 内容字符串或在失败时返回 false
     */
    protected function fetchCRL($url, $timeout = 10)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_URL => $url,
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            throw new Exception("cURL error: $error_msg");
        }
        curl_close($curl);
        return $response;
    }

    /**
     * 验证证书的 OID 扩展字段
     * @param string $cer 证书内容
     * @param string $OID 扩展字段的 OID
     * @throws VerificationException
     */
    public function verifyOID($cer, $OID)
    {
        $certificateInfo = openssl_x509_parse($cer, false);
        if ($certificateInfo === false) {
            throw new VerificationException('Failed to parse X509 certificate information');
        }
        $extensions = $certificateInfo['extensions'] ?? [];
        if (!array_key_exists($OID, $extensions)) {
            throw new VerificationException('Certificate missing extended information');
        }
    }
}