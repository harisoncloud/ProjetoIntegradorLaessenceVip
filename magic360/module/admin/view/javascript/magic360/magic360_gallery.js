jQueryMagic360(function($){
    $(document).ready(function(){
             function getToken(){
                if ($('input[name=ocVersion]').val() == 3) {
                    return '&user_token='+$('input[name=token]').val();
                } else {
                    return '&token='+$('input[name=token]').val();
                }
            }


            function getProductId () {
                return $('input[name=product_id]').val();   
            }

            function bindEvents(){
                $('.mt-icon-trash').bind('click', function(){deleteImages(false, $(this));});
            }


            function unbindEvents(){
                $('.mt-icon-trash').unbind('click');
            }


            function renderImageContent(data, renderContainer){
                //console.log(data);
                var images = data.images.split(';');
                var productId = getProductId();


                if(images.length > 0 && data.images != ""){
                    $(renderContainer).attr('data-images', data.images);
                    $('#magic360-columns').val(data.columns);
                    if(data.multiRows){
                        $('#magic360-multi-rows').attr('checked', true);
                        $('#magic360-columns').attr('disabled', false);
                    }
                    var documentFragment = $(document.createDocumentFragment());
                    $(renderContainer).empty();
                    for(var i=0, index=1; i<images.length; i++, index++){
                        var elemBlock = $('<tr id="row-' + index +'">'+
                            '<td class="image-index">#'+ index +'</td>'+
                            '<td>'+
                                '<img class="m360-image" src="'+ data['imageBaseUrl']+productId+"/"+images[i] +'" alt="'+ images[i] +'" title="'+ images[i] +'" />'+
                            '</td>'+
                            '<td>'+
                                '<span class="mt-icon-trash"></span>'+
                            '</td>'+
                        '</tr>');
                        documentFragment.append(elemBlock);
                    }

                $(renderContainer).append(documentFragment);
                bindEvents();

                }
            }


            function getContent(){
                var data = {};

                if(getProductId() == "") return;

                data['product_id'] = getProductId();
                $.ajax({
                        url: 'index.php?route=catalog/magic360_gallery/getImageContent' + getToken(),
                        type: 'POST',
                        data: data,
                        beforeSend: function(){
                            $('.ajax-overlay').show();
                        }
                    }).done(function(response){
                        //console.log(response);
                        //if(response.search('images') != -1){
                            renderImageContent(JSON.parse(response), '.show-content');    
                            $('.ajax-overlay').hide();
                        //}
                        
                    });
            }

            
            function uploadImages(files){
                //var files = event.target.files;
                var data = new FormData();

                data.append('product_id', getProductId());
                data.append('columns', $('#magic360-columns').val());
                data.append('multiRows', $('#magic360-multi-rows').is(':checked'));

                $.each(files, function(key, value)
                {
                    data.append(key, value);
                });

                $.ajax({
                    //progress status
                    xhr: function()
                    {
                    var xhr = new window.XMLHttpRequest();
                    //Upload progress
                    xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    // //console.log(percentComplete);
                    //console.log(percentComplete * 100 + '%');
                    }
                    }, false);

                    //Download progress

                    xhr.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                    var percentComplete = Math.round(evt.loaded * 100 / evt.total);
                    //Do something with download progress
                    //console.log(percentComplete + '%');
                    }

                    }, false);
                    return xhr;
                    },
                    url: 'index.php?route=catalog/magic360_gallery/uploadImages' + getToken(),
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: data,
                    //timeout: 500,
                    beforeSend: function(){
                        $('.ajax-overlay').show();
                    }
                }).done(function(response){
                    $('.ajax-overlay').hide();
                    //console.log(response);
                    renderImageContent(JSON.parse(response), '.show-content');
                    //clear list of files
                    var input = $("#filesToUpload");
                    input.replaceWith(input.val('').clone(true));


                    
                }).fail(function( jqXHR, textStatus ) {
                        $('.ajax-overlay').hide();
                        //console.log( "Request failed: " + textStatus );
                });
            }
            
            
            function deleteImages(deleteAllImagesFlag, $object){

                if($('.show-content').attr('data-images') == "") return;
                
                var data = {};
                if(deleteAllImagesFlag){
                    data['delete_all_images_flag'] = true;
                    data['images'] = $('.show-content').attr('data-images');
                    $('.show-content').attr('data-images', '');
                }else{
                    var deletedImage = $object.parent().siblings('td').children().attr('title');
                    var images = $('.show-content').attr('data-images').split(';');
                    images.splice(images.indexOf(deletedImage), 1);
                    data['images'] = images.join(';');
                    data['deleted_image'] = deletedImage;
                    $('.show-content').attr('data-images', images.join(';'));
                    data['columns'] = !$('#magic360-multi-rows').is(':checked') ? $('#magic360-columns').val()-1 : $('#magic360-columns').val();
                    data['delete_all_images_flag'] = false;
                }
                data['product_id'] = getProductId();
                $.ajax({
                        url: 'index.php?route=catalog/magic360_gallery/deleteImages' + getToken(),
                        type: 'POST',
                        data: data,
                        beforeSend: function(){
                            $('.ajax-overlay').show();
                        }
                    }).done(function(response){
                        //console.log(response);
                        if(response == 'deleted'){
                            unbindEvents();
                            $('.show-content').empty();
                            $('#magic360-multi-rows').attr('checked', false);
                            $('#magic360-columns').attr('disabled', true);
                            $('#magic360-columns').val(0);

                        }else if(response == 'updated'){
                            $object.closest('tr').remove();
                            updateIndex();
                        }
                        $('.ajax-overlay').hide();
                    });
            }

            function updateIndex(){
                var imageIndex = $('.image-index');

                //update columns only if checkbox is doesn't checked
                if(!$('#magic360-multi-rows').is(':checked')){
                    $('#magic360-columns').val(imageIndex.length);    
                }

                for(var i=0, index=1; i<imageIndex.length; i++,index++){
                    $(imageIndex[i]).text('#' + index);
                }
            }


            $('#magic360-multi-rows').on('click', updateMultiRowsCheckbox);

            function updateMultiRowsCheckbox(){
                if($('#magic360-multi-rows').is(':checked')){
                    $('#magic360-columns').attr('disabled', false);
                }else{
                    $('#magic360-columns').attr('disabled', true);
                    $('.mt-update-columns').css('display', 'none');
                    $('#magic360-columns').val($('.m360-image').length || 0);
                    updateMultiRowsColumns();
                }
            }


            $('#magic360-columns').on('keypress', showSaveButton);

            function showSaveButton(){
                if($('#magic360-multi-rows').is(':checked')){
                    $('.mt-update-columns').css('display', 'inline-block');
                }
            }

            
            $('.mt-update-columns').on('click', updateMultiRowsColumns); 
            
            function updateMultiRowsColumns(){

                data = {};

                data['product_id'] = getProductId();
                data['images'] = $('.show-content').attr('data-images');
                data['columns'] = $('#magic360-columns').val();

                if(data['columns'] == ''){
                    return;
                }


                $.ajax({
                        url: 'index.php?route=catalog/magic360_gallery/updateColumns' + getToken(),
                        type: 'POST',
                        data: data,
                        beforeSend: function(){
                            $('.ajax-overlay').show();
                        }
                    }).done(function(response){
                        //console.log(response);
                        $('.mt-update-columns').css('display', 'none');
                        clearField('#magic360-columns');
                        $('.ajax-overlay').hide();
                    });

            }
            
            function clearField(container){
                var $field = $(container);
                $field.val(Math.abs(parseInt($field.val())));
            }

            //initialization
            $('#filesToUpload').bind('change', function(event){uploadImages(event.target.files);});
            $('.mt-delete-all').bind('click', function(){deleteImages(true);});
            getContent();
        });
});