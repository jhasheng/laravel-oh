<?php


namespace Purple\OpensslHelper\Handler;

class AltNames
{
    /**
     * IP address
     * @var array
     */
    protected $ip = [];

    /**
     * Domain Name
     * @var array
     */
    protected $dns = [];

    /**
     * URL address
     * @var array
     */
    protected $url = [];

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     * @return AltNames
     */
    public function setIp(array $ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDns()
    {
        return $this->dns;
    }

    /**
     * @param mixed $dns
     * @return AltNames
     */
    public function setDns(array $dns)
    {
        $this->dns = $dns;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     * @return AltNames
     */
    public function setUrl(array $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Method toString
     *
     * Returns altnames formated for CNF files
     * @return string
     */
    public function toString()
    {
        $string = "";
        array_walk($this->dns, function (&$dns, $i) {
            $dns = sprintf('DNS.%s = %s', $i + 1, $dns);
        });
        $string .= implode(PHP_EOL, $this->dns) . PHP_EOL;

        array_walk($this->url, function (&$ip, $url) {
            $url = sprintf('URL.%s = %s', $i + 1, $url);
        });
        $string .= implode(PHP_EOL, $this->url) . PHP_EOL;

        array_walk($this->ip, function (&$ip, $i) {
            $ip = sprintf('IP.%s = %s', $i + 1, $ip);
        });
        $string .= implode(PHP_EOL, $this->ip);
        return $string;
    }
}
