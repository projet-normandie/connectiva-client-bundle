<?php

declare(strict_types=1);

namespace ProjetNormandie\ConnectivaBundle\DependencyInjection;

use Connectiva\Client;
use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ProjetNormandieConnectivaClientExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yml');

        $this->addClients($config['clients'], $container);
    }

    /**
     * @param array $clients
     * @param ContainerBuilder $container
     */
    private function addClients(array $clients, ContainerBuilder $container): void
    {
        foreach($clients as $name => $client) {
            $this->createClient(
                $name,
                $client['url'],
                $client['username'],
                $client['password'],
                $client['alias'],
                $container
            );
        }

        reset($clients);
        $this->setDefaultClient(key($clients), $container);
    }

    /**
     * @param $name
     * @param ContainerBuilder $container
     */
    private function setDefaultClient($name, ContainerBuilder $container): void
    {
        $container->setAlias('projetnormandie_connectiva.client.default', sprintf('projetnormandie_connectiva.client.%s', $name));
        $container->setAlias(Client::class, 'projetnormandie_connectiva.client.default');
    }

    /**
     * @param $name
     * @param $url
     * @param $username
     * @param $password
     * @param $alias
     * @param ContainerBuilder $container
     */
    private function createClient($name, $url, $username, $password, $alias, ContainerBuilder $container): void
    {
        $definition = new Definition('%projetnormandie_connectiva.client.class%');
        $definition->addArgument($url);
        $definition->addArgument($username);
        $definition->addArgument($password);
        $definition->setFactory(array(Client::class, 'create'));

        // Add Service to Container
        $container->setDefinition(
            sprintf('projetnormandie_connectiva.client.%s', $name),
            $definition
        );

        // If alias option is set, create a new alias
        if(null !== $alias) {
            $container->setAlias($alias, sprintf('projetnormandie_connectiva.client.%s', $name));
        }
    }
}