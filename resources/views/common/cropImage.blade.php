<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="modalLabel">Crop Image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="img-container">
               <div class="row">
                  <div class="col-md-8">
                     <img id="image" src="" class="cropper-hidden crop-img-preview">
                     <input type="hidden" name="cropMediaId" id="cropMediaId">
                     <input type="hidden" name="cropHeight" id="cropHeight" value="0">
                     <input type="hidden" name="cropWidth" id="cropWidth" value="0">
                  </div>
                  <div class="col-md-4">
                     <div class="preview-cropped-img"></div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="crop">Crop</button>
         </div>
      </div>
   </div>
</div>