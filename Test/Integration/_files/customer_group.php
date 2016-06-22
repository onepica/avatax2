<?php
/** @var \Magento\Customer\Api\GroupRepositoryInterface $groupRepository */
use Magento\TestFramework\Helper\Bootstrap;

$groupRepository = Bootstrap::getObjectManager()->create('Magento\Customer\Api\GroupRepositoryInterface');

$groupFactory = Bootstrap::getObjectManager()->create('Magento\Customer\Api\Data\GroupInterfaceFactory');
$groupDataObject = $groupFactory->create();
$groupDataObject->setCode('custom_group')->setTaxClassId(3);
$groupRepository->save($groupDataObject);
