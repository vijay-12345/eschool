<!-- Modal Header -->


            <div class="modal-header">
              <h4 class="modal-title"><?php 
              
         
           echo "Add In Forum";  
         
         ?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body"> 
            <form action="{{url('/api/v2/add_forum_post_web_panel')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/forum/'.$teacher_class_subject['id'].'/view')}}'>
              
            
            <input type='hidden' name='role' value="<?= $request['role']; ?>">
            
            <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
            <input type='hidden' name='date' value="{{date("Y/m/d")}}">
            <input type='hidden' name='teacher_class_subject_id' value="{{$teacher_class_subject['id']}}">
            <input type='hidden' name='reply_from' value="{{$request['teacher_id']}}">
            <input type='hidden' name='who_replyed' value="teacher">
            
            <div class="form-group">
                <label>Message <span class="start">*</span></label>
                <textarea rows="4" cols="80" class="form-control" name="message_content" placeholder="Enter message" ></textarea>
            </div>


              <div class="form-group">
                  <label>Select Pic <span class="start"></span></label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" name="image[]" id="pic">
                    <label class="custom-file-label" for="customFile">Choose file</label>
                  </div>
              </div>
          

            <button type="submit" class="btn btn-primary float-right submitButton">Save</button>
            </form>
            </div>
<script>
  $('select').SumoSelect({search: true});
  $('#pic').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
</script>