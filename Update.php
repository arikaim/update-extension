<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Update;

use Arikaim\Core\Extension\Extension;

/**
 * Update extension class
 */
class Update extends Extension
{
    /**
     * Install extension
     *
     * @return void
     */
    public function install()
    {
        // Control Panel       
        $this->addApiRoute('PUT','/api/update/admin/update/{type}','UpdateControlPanel','update','session');      
    
        // Console Commands
        //$this->registerConsoleCommand('QueueWorker');            
    }
    
    /**
     * UnInstall extension
     *
     * @return void
     */
    public function unInstall()
    {  
    }
}
