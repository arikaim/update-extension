/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function UpdateControlPanel() {
   
    this.update = function(type, onProgress, onSuccess, onError) {
        type = getDefaultValue(type,'composer');
        return taskProgress.put('/api/update/admin/update/' + type,{ update: true },onProgress,onSuccess,onError);          
    };

    this.check = function(type, onProgress, onSuccess, onError) {
        type = getDefaultValue(type,'composer');
        return taskProgress.put('/api/update/admin/update/' + type,{ update: false },onProgress,onSuccess,onError);             
    };

    this.init = function() {     
        arikaim.ui.tab();      
    };
}

var updateControlPanel = new UpdateControlPanel();

arikaim.component.onLoaded(function() {
    updateControlPanel.init();
});