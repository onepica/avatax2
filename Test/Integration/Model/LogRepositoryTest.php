<?php

namespace Astound\AvaTax\Test\Integration\Model;

use Astound\AvaTax\Api\Data\LogInterface;
use Astound\AvaTax\Api\LogRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class LogRepositoryTest
 *
 * @package Astound\AvaTax\Test\Integration\Model
 */
class LogRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Log repository
     *
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * LogInterfaceFactory
     *
     * @var \Astound\AvaTax\Api\Data\LogInterfaceFactory
     */
    protected $logFactory;

    /**
     * Initialize necessary objects
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->logRepository = $this->objectManager->create(LogRepositoryInterface::class);
        $this->logFactory = $this->objectManager->create('Astound\AvaTax\Api\Data\LogInterfaceFactory');
    }

    /**
     * Test saving log
     *
     * @test
     * @magentoDbIsolation enabled
     */
    public function save()
    {
        $logDataObject = $this->createLogItem();
        $logDataObject->setStoreId(1)
            ->setAdditionalInfo('additional')
            ->setCreatedAt('2016-05-17 11:25:05')
            ->setLogLevel('Success')
            ->setLogType('Calculation')
            ->setRequest('request')
            ->setResponse('response');

        $log = $this->logRepository->save($logDataObject);

        self::assertInstanceOf(LogInterface::class, $log);
        self::assertNotNull($log->getLogId());
    }

    /**
     * Test loading log entity by id
     *
     * @test
     * @magentoDbIsolation enabled
     * @covers             LogRepository::save
     */
    public function getById()
    {
        $logDataObject = $this->createLogItem();
        $logDataObject->setStoreId(1)
            ->setAdditionalInfo('additional')
            ->setCreatedAt('2016-05-17 11:25:05')
            ->setLogLevel('Success')
            ->setLogType('Calculation')
            ->setRequest('request')
            ->setResponse('response');

        $logId = $this->logRepository->save($logDataObject)->getLogId();

        $log = $this->logRepository->getById($logId);

        self::assertEquals($logId, $log->getLogId());
        self::assertEquals(1, $log->getStoreId());
        self::assertEquals('additional', $log->getAdditionalInfo());
        self::assertEquals('2016-05-17 11:25:05', $log->getCreatedAt());
        self::assertEquals('Success', $log->getLogLevel());
        self::assertEquals('Calculation', $log->getLogType());
        self::assertEquals('request', $log->getRequest());
        self::assertEquals('response', $log->getResponse());

    }

    /**
     * Test loading non exist log entity
     *
     * @test
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Avatax Log with id "-100" does not exist.
     * @covers                   LogRepository::getById
     */
    public function getByIdThrowsException()
    {
        $this->logRepository->getById(-100);
    }

    /**
     * @return LogInterface
     */
    protected function createLogItem()
    {
        return $this->logFactory->create();
    }
}
