<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Extensions\Update\Classes;

use Arikaim\Core\Queue\Traits\JobProgress;
use Arikaim\Core\Packages\Composer;
use Arikaim\Core\Extension\Extension;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\Utils;
use Closure;

/**
 * System update 
 */
class Update 
{
    use JobProgress;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * Check package for updates
     *
     * @return array
     */
    public function checkPackages(string $type): array
    {
        $result = [
            'items' => [],
            'total' => 0
        ];
        $packageManager = Arikaim::get('packages')->create($type);
        $packages = $packageManager->getPackages();

        foreach($packages as $name) {
            $package = $packageManager->createPackage($name);
            $version = $package->getVersion();
            $repository = $packageManager->getRepository($name);
            if (empty($repository) == true) {
                continue;
            }

            $lastVersion = $repository->getLastVersion();
            if (empty($lastVersion) == true) {
                continue;
            }
            if (Utils::checkVersion($version,$lastVersion) == true) {
                continue;
            }

            $item = [
                'type'            => $type,
                'name'            => $name, 
                'current_version' => $version,
                'version'         => $lastVersion
            ];
            $this->jobProgress($item);
            $result['items'][] = $item;       
            $result['total']++;   
        }

        if ($result['total'] == 0) {                  
            $result['items'] = $this->addEmptyItem();           
        }

        return $result;
    }

    /**
     * Check core packages for updates
     *
     * @return array
     */
    public function checkCorePackages(): array
    {
        $packages = Extension::loadJsonConfigFile('arikaim-packages.json','update');
        $result = [
            'items' => [],
            'total' => 0
        ];
        
        foreach ($packages as $packageName) {
            # code...
            $version = Composer::getInstalledPackageVersion(ROOT_PATH . BASE_PATH,$packageName);
            if ($version === false) {
                continue;
            }
            
            $tokens = explode('/',$packageName);
            $lastVersion = Composer::getLastVersion($tokens[0],$tokens[1]);
       
            if (Utils::checkVersion($version,$lastVersion) == false) {
                $item = [
                    'type'            => 'package',
                    'name'            => $packageName, 
                    'current_version' => $version,
                    'version'         => $lastVersion
                ];
                $this->jobProgress($item);
                $result['items'][] = $item;       
                $result['total']++;   
            }
        }
        
        if ($result['total'] == 0) {                  
            $result['items'] = $this->addEmptyItem();           
        }

        return $result;
    }


    public function updateCorePackages(): array
    {
        $result = [
            'items' => [],
            'total' => 0
        ];

        return $result;
    }

    /**
     * Check package for updates
     *
     * @return array
     */
    public function updatePackages(string $type): array
    {
        $result = [
            'items' => [],
            'total' => 0
        ];

        return $result;
    }

    /**
     * Add empty item
     *
     * @return array
     */
    protected function addEmptyItem(): array
    {
        $item = [
            'type'            => null,
            'name'            => '..', 
            'current_version' => null,
            'version'         => null
        ];           
        $this->jobProgress($item);

        return $item;
    }
}
