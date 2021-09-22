<!-- Modal Header -->


            <div class="modal-header">
              <h4 class="modal-title"><?php 
              
         if(empty($studymaterial)){
           echo "Add Study Material";  
         }else{
           echo "Update Study Material";
         }
         ?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            @if(!empty($studymaterial))  
            <form action="{{url('/api/v2/update-study-material-web-panel')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/study-material')}}'>
            @else
            <form action="{{url('/api/v2/add-study-material-web-panel')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/study-material')}}'>
            @endif  
            @if(!empty($studymaterial))
            <input type="hidden" name="id" value="<?= $studymaterial['id']; ?>"/>
            @endif
            <input type='hidden' name='role' value="<?= $request['role']; ?>">
            @if(Auth::guard('admin')->check())
              <label for="class">School<span class="start">*</span></label>
              @if(!empty($studymaterial))
                <select class="form-control" name='school_id'>
                  @foreach($schools as $school)
                    @if($notice['school_id'] == $school->id)
                      <option value="{{$school->id}}" selected="{{$school->name}}">{{ucfirst($school->name)}}</option>
                    @else
                      <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                    @endif
                  @endforeach
                </select>  
              @else
              <select class="form-control" name='school_id'>
                  <option value="">Select type</option>
                  @foreach($schools as $school)
                    <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                  @endforeach
              </select>
              @endif
            @else
            <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
            @endif

            <div class="form-group">
              <div>
                <label>Class-Section and Subject<span >*</span></label>
                <select class="form-control" name="teacher_class_subject_id" id="teacher_class_subject_id" >
                  <option value="">Select Class-Section and Subject</option>
                  
                  @foreach($teacher_class_subjects as $teacher_class_subject)
                    @if(array_key_exists("teacher_class_subject_id",$studymaterial))
                    <option {{($studymaterial['teacher_class_subject_id'] === $teacher_class_subject['id'] ? 'selected':'' ) }} value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                    @else
                    <option value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                    @endif
                    @endforeach
                </select>
              </div>
            </div>
            <div class="form-group">
              <div>
                  <label>Date <span class="start">*</span></label>
                  <div>
                      <input type='text' class="form-control" name="date" id="datepicker1" value="{{(array_key_exists("date",$studymaterial)?$studymaterial['date']:'') }}">
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
              </div> 
            </div>
          

            <div class="form-group" style="margin-bottom: 1em;">
              <label for="section">Title <span class="start">*</span></label>
              <input type="text" name="title" class="form-control"  placeholder="Enter Title " id="title" value="{{(array_key_exists("title",$studymaterial)?$studymaterial['title']:'') }}">
            </div>

            <div class="form-group">
              <label for="section"> Content <span class="start">*</span></label>
              <input type="text" name="content" class="form-control"  placeholder="Enter Content " id="content" value="{{(array_key_exists("content",$studymaterial)?$studymaterial['content']:'') }}">
            </div>


            <div class="form-group">
                <label>Select Attachment <span class="start"></span></label>
                <div class="custom-file">
                  <input type="file" class="custom-file-input" name="image[]" id="attachment" multiple>
                  <label class="custom-file-label" for="customFile">Choose file</label>
                </div>
                <?php   if($studymaterial != null){ ?>
                      <a href="<?= $attachment['file_url']; ?>" download="<?= $attachment['file_url']; ?>">
                    Download</a>
                 <?php } ?>
            </div>
           
            <button type="submit" class="btn btn-primary float-right submitButton">Save</button>
            </form>
            </div>



<script type="text/javascript">
        $(function () {
                $('#datepicker1').datepicker({
                  format: 'yyyy-mm-dd'
                });
            });
        $('#attachment').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
    </script>