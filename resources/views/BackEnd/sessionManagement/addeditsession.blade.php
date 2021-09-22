<!-- Modal Header -->


            <div class="modal-header">
              <h4 class="modal-title"><?php 
              
         if(empty($session)){
           echo "Add Session";  
         }else{
           echo "Update Session";
         }
         ?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            @if(!empty($session))  
            <form action="{{url('/api/v2/update-session')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/session')}}'>
            @else
            <form action="{{url('/api/v2/add-session')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/session')}}'>
            @endif  
            @if(!empty($session))
            <input type="hidden" name="id" value="<?= $session['id']; ?>"/>
            <input type="hidden" name="session_id" value="<?= $session['id']; ?>"/>
            @endif

            <input type='hidden' name='role' value="<?= $request['role']; ?>">
            <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
            
                <div class="form-group">
                    <div>
                    <label>Class-Section and Subject<span >*</span></label>
                    <select class="form-control" name="teacher_class_subject_id" id="teacher_class_subject_id" >
                      <option value="">Select Class-Section and Subject</option>
                      @foreach($teacher_class_subjects as $teacher_class_subject)
                        @if(array_key_exists("teacher_class_subject_id",$session))
                        <option {{($session['teacher_class_subject_id'] === $teacher_class_subject['id'] ? 'selected':'' ) }} value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                        @else
                        <option value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <div>
                      <label>Topic <span class="start">*</span></label>
                      <div>
                          <input type='text' class="form-control" placeholder="Enter Topic" name="topic" value="{{(array_key_exists("topic",$session)?$session['topic']:'') }}">
                      </div>
                  </div> 
                </div>

                <div class="form-group">
                  <div>
                      <label>Start Date <span class="start">*</span></label>
                      <div>
                          <input type='text' class="form-control" placeholder="Start Date" name="date" value="{{(array_key_exists("date",$session)?$session['date']:'') }}" id="datepicker">
                      </div>
                  </div> 
                </div>
            
                <div class="row" style="margin-bottom: 1em;">
                <div class="col-sm-6">
                      <label>Start Time<span class="start">*</span></label>
                      <div>
                          <input type='text' class="form-control" placeholder="Start Time" name="start_time" value="{{(array_key_exists("start_time",$session)?$session['start_time']:'') }}" id="start_time">
                      </div>
                </div>
                <div class="col-sm-6">
                      <label>End Time<span class="start">*</span></label>
                      <div>
                          <input type='text' class="form-control" placeholder="End Time" name="end_time" value="{{(array_key_exists("end_time",$session)?$session['end_time']:'') }}" id="end_time">
                      </div>
                </div>
              </div>

              <div class="form-group">
                <label for="section">Online Class Url</label>
                <input type="text" name="online_class_url" class="form-control" value="{{(array_key_exists("online_class_url",$session)?$session['online_class_url']:'') }}" placeholder="Enter Online Class Url " id="online_class_url">
              </div>

              <div class="form-group">
                <label for="section">Meeting Id</label>
                <input type="text" name="meeting_id" class="form-control" value="{{(array_key_exists("meeting_id",$session)?$session['meeting_id']:'') }}" placeholder="Enter Meeting Id " id="meeting_id">
              </div>

              <div class="form-group">
                <label for="section">Session Password</label>
                <input type="text" name="password" class="form-control" value="{{(array_key_exists("password",$session)?$session['password']:'') }}" placeholder="Enter Session Password " id="password">
              </div>

              
            <button type="submit" class="btn btn-primary float-right submitButton">Save</button>
            </form>
            </div>


<script>
        $(function () {
        var todayDate = new Date();
        $('#datepicker').datepicker({
                  format: 'yyyy-mm-dd',
                   startDate: new Date(),
                   autoclose:true
                });

        $('#start_time').datetimepicker({
                  startDate:new Date(),
                   step: 15
               });
                $('#end_time').datetimepicker({
                startDate:new Date(),   
                step: 15
               });
    });
      
                
            
</script>