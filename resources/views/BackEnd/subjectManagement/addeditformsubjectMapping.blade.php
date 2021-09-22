<!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title"><?php 
         
                  if(!empty($subject_class)){
                   echo "Update Subject Mapping";
                  }else{
                    echo "Add Subject Mapping";
                  }
              ?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
            <form action="{{url('/api/v2/add_update_subject_class')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/mapping')}}'>
            <?php 
              if(!empty($subject_class)){ ?>
                 <input type="hidden" name="id" value="<?= $subject_class['id']; ?>"/>
              <?php } ?>
            <input type='hidden' name='role' value="<?= $request['role']; ?>">
            @if(Auth::guard('admin')->check())
                <label for="class">School<span class="start">*</span></label>
                  @if(!empty($subject_class))
                      <select class="form-control" name='school_id'>
                        @foreach($schools as $school)
                          @if($subject_class['school_id'] == $school->id)
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
                    <label>Class-Section Name <span class="start">*</span></label>
                    <select class="form-control" name="class_section_id">
                        <option value="">Select class and section</option>
                        @if(!empty($classSections))
                        @foreach($classSections as $classSection)
                        @if(array_key_exists("class_section_id",$subject_class))
                        <option {{($subject_class['class_section_id'] == $classSection->id ? 'selected':'' ) }} value="<?= $classSection->id; ?>"><?= $classSection->	class_name.'-'.$classSection->section_name; ?></option>
                        @else
                        <option value="<?= $classSection->id; ?>"><?= $classSection->	class_name.'-'.$classSection->section_name; ?></option>
                        @endif
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                <label>Subject Name <span class="start">*</span></label>
                <select class="form-control" name="subject_id">
                    <option value="">Select subject</option>
                    @if(!empty($subjects))
                    @foreach($subjects as $subject)
                    @if(array_key_exists("subject_id",$subject_class))
                    <option {{($subject_class['subject_id'] === $subject->id ? 'selected':'' ) }} value="<?= $subject->id; ?>"><?= $subject->subject_name; ?></option>
                    @else
                    <option value="<?= $subject->id; ?>"><?= $subject->subject_name; ?></option>
                    @endif
                    @endforeach
                    @endif
                </select>
                </div>
              {{ csrf_field()}}
              <button type="submit" class="btn btn-primary float-right submitButton">Save</button>
            </form>
            </div>

            <script>
  $('select').SumoSelect({search: true});
</script>