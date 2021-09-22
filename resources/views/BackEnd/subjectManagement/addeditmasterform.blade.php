    <!-- Modal Header -->
    <?php 
      error_reporting(0);
    ?>
    <div class="modal-header">
      <h4 class="modal-title">
      
        <?php 
          if(empty($subject_master)){
            echo "Add Subject";  
          }else{
            echo "Update Subject";
          }
        ?>
      </h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    
    <!-- Modal body -->
    <div class="modal-body" >
              <?php if(!empty($subject_master)){ ?>
                <form action="{{url('/api/v2/update_subject_master')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/subject')}}'>
                <input type="hidden" name="id" value="<?= $subject_master->id; ?>"/>
               <?php }else{ ?>
                  <form action="{{url('/api/v2/add_subject_master')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/subject')}}'>
              <?php } ?>
                  <input type="hidden" name="type" value="student"/>
                  <input type='hidden' name='role' value="<?= $request['role']; ?>">
                  <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
                  <div class="form-group">
                    <label for="class">Subject <span class="start">*</span></label>
                    <input type="text" name="subject_name" class="form-control" value="{{ $subject_master->subject_name }}" placeholder="Enter subject name" id="subject">
                  </div>
                <button class="btn btn-primary float-right submitButton">Save</button>
          </form>

    </div> 
           
<script>
  $('select').SumoSelect({search: true});
</script>