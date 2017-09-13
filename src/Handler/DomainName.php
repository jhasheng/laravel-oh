<?php


namespace Purple\OpensslHelper\Handler;

class DomainName
{

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param mixed $countryName
     * @return DomainName
     */
    public function setCountryName($countryName)
    {
        $this->config['countryName'] = $countryName;

        return $this;
    }

    /**
     * @param mixed $stateOrProvinceName
     * @return DomainName
     */
    public function setStateOrProvinceName($stateOrProvinceName)
    {
        $this->config['stateOrProvinceName'] = $stateOrProvinceName;

        return $this;
    }

    /**
     * @param mixed $localityName
     * @return DomainName
     */
    public function setLocalityName($localityName)
    {
        $this->config['localityName'] = $localityName;

        return $this;
    }

    /**
     * @param mixed $organizationName
     * @return DomainName
     */
    public function setOrganizationName($organizationName)
    {
        $this->config['organizationName'] = $organizationName;
        return $this;
    }

    /**
     * @param mixed $organizationalUnitName
     * @return DomainName
     */
    public function setOrganizationalUnitName($organizationalUnitName)
    {
        $this->config['organizationalUnitName'] = $organizationalUnitName;

        return $this;
    }

    /**
     * @param mixed $commonName
     * @return DomainName
     */
    public function setCommonName($commonName)
    {
        $this->config['commonName'] = $commonName;

        return $this;
    }

    /**
     * @param mixed $emailAddress
     * @return DomainName
     */
    public function setEmailAddress($emailAddress)
    {
        $this->config['emailAddress'] = $emailAddress;

        return $this;
    }

    public function serialize()
    {
        return json_encode($this->config);
    }

    public function getConfig()
    {
        return $this->config;
    }
}
