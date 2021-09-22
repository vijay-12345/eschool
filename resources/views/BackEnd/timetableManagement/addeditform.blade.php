    <!-- Modal Header -->
    <?php 
      error_reporting(0);
    ?>
    <div class="modal-header">
      <h4 class="modal-title">
      
        <?php 
          if(empty($time_table)){
            echo "Add Time Table";  
          }else{
            echo "Update Time Table";
          }
        ?>
      </h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    
    <!-- Modal body -->
    <div class="modal-body" >
             <form action="{{url('/api/v2/add_update_time_table')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/timetable')}}'>
              <?php   if(!empty($time_table)){ ?>
                <input type="hidden" name="id" value="<?= $time_table['id']; ?>"/>
               <?php } ?>
                  
                  <input type='hidden' name='role' value="<?= $request['role']; ?>">
                  @if(Auth::guard('admin')->check())
                    <label for="class">School<span class="start">*</span></label>
                      @if(!empty($time_table))
                        <select class="form-control" name='school_id'>
                          @foreach($schools as $school)
                            @if($time_table['school_id'] == $school->id)
                              <option value="{{$school->id}}" selected="{{$school->name}}">{{ucfirst($school->name)}}</option>
                            @else
                              <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                            @endif
                          @endforeach
                        </select>  
                      @else
                      <select class="form-control" name='school_id'>
                          <option value="">Select School</option>
                          @foreach($schools as $school)
                            <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                          @endforeach
                      </select>
                      @endif
                 @else
                  <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
                 @endif
              <div class="form-group">
                <label>Type <span class="start">*</span></label>
                    <select class="form-control" name='type'>
                        @if(!empty($time_table))
                          <option value="{{$time_table['type']}}" selected="{{$time_table['type']}}">{{ucfirst($time_table['type'])}}</option>
                          @if($time_table['type'] == 'teacher')
                            <option value="student">Student</option>
                          @else
                            <option value="teacher">Teacher</option>
                          @endif  
                        @else
                          <option value="">Select Type</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        @endif
                    </select>
              </div>    
              <div class="form-group">
                <label>Title <span class="start">*</span></label>
                   <input type="text" name="content" class="form-control" placeholder="Enter title" value="<?=$time_table['content']?>"/>
              </div>

                <div class="form-group">
                <label>Class-secion <span class="start">*</span></label>
                    <select class="form-control" name="class_section_id">
                          <option value="">Select class and section</option>
                          <?php foreach($class_section as $classSection){ ?>
                              <option <?= ($classSection->id === $time_table['class_section_id'] ? 'selected':'' ) ?> value="<?= $classSection->id; ?>"><?= $classSection->class_name.'-'.$classSection->section_name; ?></option>
                          <?php  } ?>
                    </select>
                </div>
                <div class="form-group">
                <label>Upload Time Table </label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="image[]">
                      <label class="custom-file-label" for="customFile">Choose file</label>
                </div>
                <?php   if(!empty($time_table['file_url']['file_url'])){ ?>
                  <a href="<?= $time_table['file_url']['file_url']; ?>" download="<?= $time_table['file_url']['file_url']; ?>">Download</a>
               <?php } ?>
                 
                </div>
                
                <button class="btn btn-primary float-right submitButton">Save</button>
          </form>

    </div> 
           
<script>
  $('select').SumoSelect({search: true});
</script>