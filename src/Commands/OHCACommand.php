<?php

namespace Purple\OpensslHelper\Commands;

use Illuminate\Console\Command;

class OHCACommand extends Command
{
    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'oh:ca
        {--U|organizationUnitName=Test : organization unit name}
        {--C|countrName=CN : country name}
        {--N|name=Test : ca alias name}
        {--T|type=ca : ca type}
        {--R|rootCA=Test : Root CA}
        {--A|commonName=example.com : common name}
        {--I|IP=* : alternative IP}
        {--D|DNS=* : alternative DNS}
        {--L|URL=* : alternative URL}
        {--O|organizationName=Test : organization name}';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'Generate ROOT CA';


    public function handle()
    {
        $name                   = $this->option('name');
        $type                   = $this->option('type');
        $rootCA                 = $this->option('rootCA');
        $commonName             = $this->option('commonName');
        $countryName            = $this->option('countrName');
        $organizationName       = $this->option('organizationName');
        $organizationUnitName   = $this->option('organizationUnitName');

        $ip     = $this->option('IP');
        $url    = $this->option('URL');
        $dns    = $this->option('DNS');

        $cf = new \Purple\OpensslHelper\Factory\CertificateFactory();
        $cf->domainName()
            ->setOrganizationName($organizationName)
            ->setCountryName($countryName)
            ->setCommonName($commonName);

        if ($type == 'intermediate') {
            $name = sprintf('%s/%s', $rootCA, $name);
        } elseif ($type == 'server') {
            $name = sprintf('%s/%s', $rootCA, $name);
            $cf->getAltNames()->setDns($dns)->setUrl($url)->setIp($ip);
        }
        $savePath = $cf->setType($type)
            ->setCa($rootCA)
            ->setName($name)
            ->sign()->toFile();

        if ($savePath) {
            $this->info(sprintf('OK, save path: %s', $savePath));
        } else {
            $this->info('generate certificate failed!');
        }
    }
}
