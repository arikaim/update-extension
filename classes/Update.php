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
use Arikaim\Core\Extension\Extension;
use Arikaim\Core\Arikaim;
use Arikaim\Core\Utils\Utils;

/**
 * System update 
 */
class Update 
{
    use JobProgress;

    /**
     * Check or update packages
     *
     * @param string $type
     * @param bool $update
     * @return array
     */
    public function checkPackages(string $type, bool $update = false): array
    {
        $result = [
            'items' => [],
            'total' => 0
        ];
        $packageManager = Arikaim::get('packages')->create($type);
        if ($type == 'composer') {
            $packages = Extension::loadJsonConfigFile('arikaim-packages.json','update');
        } else {
            $packages = $packageManager->getPackages();
        }
    
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
            echo " $name > $type > $version > $lastVersion ";
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
     * Check or update core packages
     *
     * @return array
     */
    public function corePackages(bool $update = false): array
    {
        $packages = Extension::loadJsonConfigFile('arikaim-packages.json','update');
        $result = [
            'items' => [],
            'total' => 0
        ];
        
        foreach ($packages as $packageName) {
           
            $version = Composer::getInstalledPackageVersion($packageName);
            if ($version === false) {
                continue;
            }
            
            $tokens = explode('/',$packageName);
            $lastVersion = Composer::getLastVersion($tokens[0],$tokens[1]);
       
            if (Utils::checkVersion($version,$lastVersion) == false) {
                // package have new verison
                $item = [
                    'type'            => 'package',
                    'name'            => $packageName, 
                    'current_version' => $version,
                    'version'         => $lastVersion
                ];

                if ($update == true) {
                    echo "run update: " . $packageName;
                    Composer::run('update',[$packageName]);
                    $version = Composer::getInstalledPackageVersion($packageName);
                    if (Utils::checkVersion($version,$lastVersion) == true) {
                        // updated
                        $this->jobProgress($item);
                        $result['items'][] = $item;  
                        $result['total']++;   
                    } else {
                        // error updating
                        $this->jobProgressError($item);
                    }
                } else {
                    $this->jobProgress($item);
                    $result['items'][] = $item;  
                    $result['total']++;    
                }
            }
            
            $success = ($update == true) ? $repository->install($lastVersion) : true;

            if ($success == true) {
                $this->jobProgress($item);
                $result['items'][] = $item;       
                $result['total']++; 
            } else {
                $this->jobProgressError($item);
            }
        }

        if ($result['total'] == 0) {                  
            $result['items'][] = $this->addEmptyItem();           
        }

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
