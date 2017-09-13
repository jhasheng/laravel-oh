<?php
/**
 * This file is part of MayMeow/encrypt project
 * Copyright (c) 2017 Charlotta Jung
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * @copyright Copyright (c) Charlotta MayMeow Jung
 * @link      http://maymeow.click
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @project may-encrypt
 * @file CertificateFactory.php
 */

namespace Purple\OpensslHelper\Factory;

use Purple\OpensslHelper\Handler\AltNames;
use Purple\OpensslHelper\Handler\DomainName;
use Purple\OpensslHelper\Handler\SignedCertificate;
use Symfony\Component\Yaml\Yaml;

class CertificateFactory implements CertificateFactoryInterface
{
    /**
     * @var DomainName
     */
    protected $domainName;

    /**
     * Alternative names for certificate
     * DNS, IP, URL
     *
     * @var
     */
    protected $altNames;

    /**
     * Loaded config from encrypt.yml
     *
     * @var
     */
    protected $config;

    /**
     * Configuration for certificate based on certificate type
     *
     * @var array
     */
    protected $certConfigure = [];

    /**
     * Certification Authority name
     *
     * @var string caName
     */
    protected $caName;

    /**
     * Password for CA's private key
     *
     * @var string caPassword
     */
    protected $caPassword;

    /**
     * Name for certificate files
     *
     * @var
     */
    protected $fileName;

    /**
     * Type of certificate
     * User, server, ca or intermediate
     *
     * @var
     */
    protected $type;

    /**
     * Certificate model
     * Here will be stored all required variables, keys, csr and certificate
     *
     * @var
     */
    protected $crt;

    /**
     * Default template with certificate configurations
     *
     * @var array typeConfigurations
     */
    protected $typeConfigurations;

    /**
     * Path where stored all certificates
     * @var string $caDataRoot
     */
    protected $caDataRoot;

    public function __construct($path = null)
    {
        if (null === $path) {
            $path = storage_path('openssl');
        }

        $config = require __DIR__ . '/../Config/openssl.php';
        if (file_exists(config_path('openssl.php'))) {
            $config = array_merge(require config_path('openssl.php'), $config);
        }
        $this->config               = $config;
        $this->caDataRoot           = sprintf('%s/%s', $path, 'ca');
        if (!is_dir($this->caDataRoot)) {
            mkdir($this->caDataRoot, 0777, true);
        }

        $templateRoot = sprintf('%s/template', $path);
        if (!is_dir($templateRoot)) {
            mkdir($templateRoot, 0777, true);
        }
        $this->typeConfigurations   = [
            'ca'            => sprintf('%s/%s', $templateRoot, 'ca_certificate.cnf'),
            'user'          => sprintf('%s/%s', $templateRoot, 'intermediate_certificate.cnf'),
            'server'        => sprintf('%s/%s', $templateRoot, 'intermediate_certificate.cnf'),
            'intermediate'  => sprintf('%s/%s', $templateRoot, 'intermediate_certificate.cnf')
        ];
    }

    /**
     * Sets type of certificate
     *
     * @param $type
     * @param null $options
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        $this->certConfigure = [
            'config' => $this->typeConfigurations[$this->type],
            'x509_extensions' => $this->getConfig('x509_extensions'),
            'private_key_bits' => $this->config['default']['private_key_bits']
        ];

        return $this;
    }

    /**
     * Function SetCa
     * Functions to set CA name to use for signing certificate
     *
     * @param null $name
     * @param null $password
     * @return $this
     */
    public function setCa($name = null, $password = null)
    {
        $this->caName = $name;
        $this->caPassword = $password;

        return $this;
    }

    /**
     * Function DomainName
     * Returns Domain name
     *
     * @return DomainName
     */
    public function domainName()
    {
        if (!$this->domainName) {
            $this->domainName = new DomainName();
        }

        return $this->domainName;
    }

    /**
     * Method getAltNames
     * returns AltNames
     *
     * @return AltNames
     */
    public function getAltNames()
    {
        if (!$this->altNames) {
            $this->altNames = new AltNames();
        }

        return $this->altNames;
    }

    /**
     * Function SetName
     * Set name of certificate file
     *
     * @param null $name
     * @return $this
     */
    public function setName($name = null)
    {
        $this->fileName = sprintf('%s/%s', $this->caDataRoot, $name);

        return $this;
    }

    /**
     * Sign certificate file and export tem to disk
     */
    public function sign()
    {
        $this->altConfiguration();
        $selfPrivKey = openssl_pkey_new($this->certConfigure);
        $csr = openssl_csr_new($this->domainName->getConfig(), $selfPrivKey, $this->certConfigure);
        $caCert = null;
        // 如果有CA名称，说明是需要进行子CA证书签发或者域名证签发
        if (!$this->caName == null) {
            $caCert = $this->getPublicKey($this->caName);
            $privKey = $this->getPrivateKey($this->caName);
        } else {
            $privKey = $selfPrivKey;
        }
        // 自签
        $signedCert = openssl_csr_sign($csr, $caCert, $privKey, $this->getConfig('daysvalid'), $this->certConfigure, time());
        $this->crt = new SignedCertificate($csr, $selfPrivKey, $signedCert);

        return $this;
    }

    /**
     * Create request for server signing
     * For Client App
     *
     * @return string
     */
    public function createRequest()
    {
        $this->crt = new SignedCertificate();
        $this->crt->setPrivateKey(openssl_pkey_new($this->certConfigure));

        $privKey = $this->crt->getPrivateKey();
        $this->crt->setCsr(openssl_csr_new($this->domainName()->get(), $privKey, $this->certConfigure));

        $request = json_encode([
            'csr' => $this->crt->getCsr()
        ]);

        // Send CSR to server and wait for signing
        $response = $this->signWithServer($request);
        $response = json_decode($response);
        $this->crt->setSignedCert(openssl_x509_read($response->certificate));

        return $this;
    }

    /**
     * Export certificate to file
     *
     * @param null $pkcs12
     */
    public function toFile($pkcs12 = false)
    {
        if (!file_exists($this->fileName)) {
            mkdir($this->fileName, 0777, true);
        }
        file_put_contents($this->fileName . '/code.txt', $this->crt->getEncryptionPass());
        file_put_contents($this->fileName . '/req.pem', $this->crt->getCsr());
        openssl_x509_export_to_file($this->crt->getSignedCert(), $this->fileName . '/cert.crt');
        openssl_pkey_export_to_file($this->crt->getPrivateKey(), $this->fileName . '/key.pem', $this->crt->getEncryptionPass(), $this->certConfigure);

        if ($pkcs12) {
            openssl_pkcs12_export_to_file($this->crt->getSignedCert(), $this->fileName . '/cert.pfx', $this->crt->getPrivateKey(), $this->crt->getEncryptionPass(), $this->certConfigure);
        }
    }


    /**
     * Sign certificate from client request
     * For Server app
     *
     * @return $this
     */
    public function signWithServer($request)
    {
        //server
        $clientRequest = json_decode($request);
        $this->crt->setSignedCert(openssl_csr_sign($clientRequest->csr, $this->getCaCert(), $this->getCaKey(), $this->getConfig('daysvalid'), $this->certConfigure, time()));

        // return signed file to user
        openssl_x509_export($this->crt->getSignedCert(), $clientCertificate);

        return json_encode(['certificate' => $clientCertificate]);
    }

    /**
     * Return certificates setting
     *
     * @param null $key
     * @return mixed
     */
    protected function getConfig($key)
    {
        return $this->config['certificates'][$this->type][$key];
    }

    /**
     * 获取私钥
     *
     * @param $caName
     * @param $caPassword
     * @return array
     */
    protected function getPrivateKey($caName)
    {
        return [
            file_get_contents(sprintf('%s/%s/%s', $this->caDataRoot, $caName, 'key.pem')),
            file_get_contents(sprintf('%s/%s/%s', $this->caDataRoot, $caName, 'code.txt'))
        ];
    }

    /**
     * 获取公钥
     *
     * @param null $caName
     * @return bool|string
     */
    protected function getPublicKey($caName)
    {
        return file_get_contents(sprintf('%s/%s/%s', $this->caDataRoot, $caName, 'cert.crt'));
    }

    /**
     * 备选配置生成
     *
     */
    protected function altConfiguration()
    {
        $cnfFile = file_get_contents($this->certConfigure['config']);
        if ($this->altNames) {
            $cnfFile .= $this->altNames->toString();
            if (!is_dir($this->fileName)) {
                mkdir($this->fileName, 0777, true);
            }
            $altFileName = sprintf('%s/%s', $this->fileName, 'config.cnf');
            $this->certConfigure['config'] = $altFileName;
            if (!file_exists($altFileName)) {
                touch($altFileName);
            }
            file_put_contents($altFileName, $cnfFile);
        }
    }
}
