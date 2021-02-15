/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function UpdateControlPanel() {
   
    this.update = function(type, onProgress, onSuccess, onError) {
        type = getDefaultValue(type,'packages');
        return taskProgress.put('/api/update/admin/update/' + type,{},onProgress,onSuccess,onError);          
    };

    this.check = function(type, onProgress, onSuccess, onError) {
        type = getDefaultValue(type,'packages');
        return taskProgress.put('/api/update/admin/check/' + type,{},onProgress,onSuccess,onError);             
    };

    this.init = function() {     
        arikaim.ui.tab();      
    };
}

var updateControlPanel = new UpdateControlPanel();

$(document).ready(function() {
    updateControlPanel.init();
});