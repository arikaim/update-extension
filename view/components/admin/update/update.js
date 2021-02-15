/**
 *  Arikaim
 *  @copyright  Copyright (c) Konstantin Atanasov <info@arikaim.com>
 *  @license    http://www.arikaim.com/license
 *  http://www.arikaim.com
 */
'use strict';

function UpdateView() {
    var self = this;

    this.init = function() {

        $('#update_progress').progress({
            duration : 200,
            total    : 100
        });

        this.initButtons();      
    }

    this.initButtons = function() {
        arikaim.ui.button('.check-update',function(element) {          
            $('#update_content').html('');
            $('#update_content').hide();

            $('#update_progress').progress('reset');          
            $('#progress_content').show();

            return self.checkItems('composer',function() {   
                $(element).addClass('loading disabled');            
                self.checkItems('library',function() {
                    self.checkItems('module',function() {
                        self.checkItems('extension',function() {                           
                            $(element).removeClass('loading disabled');                                  
                        })
                    });
                });
            });
        });
    };

    this.initUpdateButton = function() {
        arikaim.ui.button('.update-button',function(element) {     
            var type = $(element).attr('type');

            $('#update_content').html('');
            $('#update_content').hide();

            $('#update_progress').progress('reset');          
            $('#progress_content').show();

            return self.updateItems(type,function() {               
              
            });
        });
    };

    this.checkItems = function(type, onSuccess) {
        return updateControlPanel.check(type,function(result) {                
            $('#update_progress').progress('increment');             
         },function(result) {      
            $('#update_content').show();

            arikaim.page.loadContent({
                id: 'update_content',
                component: "update::admin.update.items",
                append: true,
                params: { 
                    type: type,
                    total: result.total,
                    items: result.items 
                }
            },function(result) {
                callFunction(onSuccess);
            });

         },function(error) {
             console.log(error);
         });
    };
   
    this.updateItems = function(type, onSuccess) {
        return updateControlPanel.update(type,function(result) {                
            $('#update_progress').progress('increment');             
         },function(result) {      
            $('#update_content').show();

            arikaim.page.loadContent({
                id: 'update_content',
                component: "update::admin.update.items",
                append: true,
                params: { 
                    type: type,
                    total: result.total,
                    items: result.items 
                }
            },function(result) {
                callFunction(onSuccess);
            });

         },function(error) {
             console.log(error);
         });
    };
}

var updateView = new UpdateView();

$(document).ready(function() {
    updateView.init();
});