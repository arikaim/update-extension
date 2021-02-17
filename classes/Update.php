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
