import('../../js/common/uploadmedia.js').then(media => {
    window.mediaIdArr = []; 
    window.mediaArr = {};   
    media.addOrUpdateProduct('update');
    media.updateSortable('.sortable');
    $('.filebox').each(function(key,ele){
        mediaIdArr.push(parseInt($(ele).attr('data-mid')));
    });
});