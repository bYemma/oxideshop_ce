<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ComponentInstaller;
use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Facts\Facts;
use OxidEsales\TestingLibrary\UnitTestCase;

class ComponentInstallerTest extends UnitTestCase
{
    use ContainerTrait;

    private $servicesFilePath = 'Fixtures/services.yaml';

    public function testInstall(): void
    {
        $installer = $this->createInstaller();
        $installer->install(__DIR__ . '/Fixtures');

        $this->assertTrue($this->doesServiceLineExists());
    }

    public function testUpdate(): void
    {
        $installer = $this->createInstaller();
        $installer->update(__DIR__ . '/Fixtures');

        $this->assertTrue($this->doesServiceLineExists());
    }
    
    private function createInstaller(): ComponentInstaller
    {
        $packageStub = $this->getMockBuilder(Package::class)->disableOriginalConstructor()->getMock();

        return new ComponentInstaller(
            new NullIO(),
            (new Facts())->getShopRootPath(),
            $packageStub
        );
    }

    private function doesServiceLineExists(): bool
    {
        $context = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);
        $contentsOfProjectFile = file_get_contents(
            $context->getGeneratedServicesFilePath()
        );

        return (bool)strpos($contentsOfProjectFile, $this->servicesFilePath);
    }
}
