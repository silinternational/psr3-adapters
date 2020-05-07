<?php
namespace Sil\Psr3Adapters\tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel as PsrLogLevel;
use Sil\Psr3Adapters\Psr3ConsoleLogger;
use Sil\Psr3Adapters\Psr3SamlLogger;
use Sil\Psr3Adapters\Psr3SyslogLogger;
use Sil\Psr3Adapters\Psr3Yii2Logger;

class Psr3EveryLoggerTest extends TestCase
{
    // Excluding Psr3Yii2Logger, because it needs a rework,
    // and this is just to get console working

    public function testLogKnownLogLevelConsole()
    {
        $this->checkSpecificLogger(new Psr3ConsoleLogger());
    }

    public function testLogKnownLogLevelSyslog()
    {
        $this->checkSpecificLogger(new Psr3SyslogLogger());
    }

    public function testLogKnownLogLevelSaml()
    {
        putenv('SIMPLESAMLPHP_CONFIG_DIR=../vendor/simplesamlphp/simplesamlphp/config-templates/');
        $this->checkSpecificLogger(new Psr3SamlLogger());
    }

    /**
     * @param Psr3SamlLogger|Psr3SyslogLogger|Psr3ConsoleLogger $logger
     */
    private function checkSpecificLogger($logger)
    {
        $this->assertIsObject(
            $logger,
            sprintf(
                'Expected an object for the logger for the %s test',
                get_class($logger)
            )
        );

        $psrLevels = [
            PsrLogLevel::EMERGENCY,
            PsrLogLevel::ALERT,
            PsrLogLevel::CRITICAL,
            PsrLogLevel::ERROR,
            PsrLogLevel::WARNING,
            PsrLogLevel::NOTICE,
            PsrLogLevel::INFO,
            PsrLogLevel::DEBUG,
        ];
        
        foreach ($psrLevels as $psrLevel) {
            // if fails ungracefully, then test failed.
            // Saml's DEBUG and INFO don't output by default.
            $logger->log($psrLevel, sprintf('Some %s message', $psrLevel));
        }
    }

    // Console won't trigger exception on unknown level.

    public function testLogUnknownLogLevelSaml()
    {
        $logger = new Psr3SamlLogger();
        $this->expectException('\Psr\Log\InvalidArgumentException');
        $logger->log('unknown', 'A test message');
    }

    public function testLogUnknownLogLevelSyslog()
    {
        $logger = new Psr3SyslogLogger('testName', null);
        $this->expectException('\Psr\Log\InvalidArgumentException');
        $logger->log('unknown', 'A test message');
    }

    public function testLogUnknownLogLevelYii2()
    {
        $logger = new Psr3Yii2Logger();
        $this->expectException('\Psr\Log\InvalidArgumentException');
        $logger->log('unknown', 'A test message');
    }
}