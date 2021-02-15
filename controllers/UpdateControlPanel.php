<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Extensions\Update\Controllers;

use Arikaim\Core\Controllers\ControlPanelApiController;
use Arikaim\Extensions\Update\Classes\Update;
use Arikaim\Core\Controllers\Traits\TaskProgress;

/**
 * Update control panel controler
*/
class UpdateControlPanel extends ControlPanelApiController
{
    use TaskProgress;

    /**
     * Init controller
     *
     * @return void
     */
    public function init()
    {
        $this->loadMessages('update::admin.messages');
    }

    /**
     * Update
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function updateController($request, $response, $data) 
    {         
        $this->onDataValid(function($data) {
            $type = $data->get('type','packages');

            $update = new Update();
            $update->onJobProgress(function($item) {
                $this->clearResult();                        
                $this->setResponse(true,function() use($item) {
                    $this
                        ->field('package',$item['package'])
                        ->field('type',$item['type'])
                        ->field('version',$item['version']);
                },'');
                return $this->sendProgressResponse();    
            });

            $this->initTaskProgress();

            $result = null;
            switch ($type) {
                case 'composer': 
                    $result = $update->corePackages(true);
                    break;
                default: 
                    $result = $update->updatePackages($type);
                    break;
            }
         
            $this->taskProgressEnd();

            $this->setResponse(\is_array($result),function() use($result,$type) {
                $this
                    ->field('items',$result['items'])
                    ->field('total',$result['total'])
                    ->message('Update ' . $type);
            },'Error update check.');               
        });
        $data->validate();        
    }

    /**
     * Check for updates
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
    */
    public function checkUpdateController($request, $response, $data) 
    {         
        $this->onDataValid(function($data) {
            $type = $data->get('type','composer');

            $update = new Update();
            $update->onJobProgress(function($item) {
                $this->clearResult();                        
                $this->setResponse(true,function() use($item) {
                    $this
                        ->field('package',$item['package'])
                        ->field('type',$item['type'])
                        ->field('version',$item['version']);
                },'');
                return $this->sendProgressResponse();    
            });

            $this->initTaskProgress();
            
            $result = $update->checkPackages($type);

            $this->taskProgressEnd();
           
            $this->setResponse(\is_array($result),function() use($result) {
                $this
                    ->field('items',$result['items'])
                    ->field('total',$result['total'])
                    ->message('Check for new verison.');
            },'Error update check.');        
        });
        $data->validate();        
    }
}
