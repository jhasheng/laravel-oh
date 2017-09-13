<?php


namespace Purple\OpensslHelper\Handler;

class SignedCertificate
{
    protected $csr;

    protected $privateKey;

    protected $signedCert;

    protected $encryptionPass;

    public function __construct($csr, $privateKey, $signedCert)
    {
        $this->csr = $csr;
        $this->privateKey = $privateKey;
        $this->signedCert = $signedCert;
        $this->encryptionPass = rand(100000, 999999);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param mixed $privateKey
     * @return SignedCertificate
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignedCert()
    {
        return $this->signedCert;
    }

    /**
     * @param mixed $signedCert
     * @return SignedCertificate
     */
    public function setSignedCert($signedCert)
    {
        $this->signedCert = $signedCert;
        return $this;
    }

    /**
     * @return int
     */
    public function getEncryptionPass()
    {
        return $this->encryptionPass;
    }

    /**
     * @param int $encryptionPass
     * @return SignedCertificate
     */
    public function setEncryptionPass($encryptionPass)
    {
        $this->encryptionPass = $encryptionPass;
        return $this;
    }

    public function getCsr()
    {
        openssl_csr_export($this->csr, $response);

        return $response;
    }

    public function setCsr($csr)
    {
        $this->csr = $csr;
        return $this;
    }
}
