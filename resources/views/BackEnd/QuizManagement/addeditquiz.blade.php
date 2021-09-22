<!-- Modal Header -->
<style>
    .parent-select .SumoSelect{
        width: 100% !important
    }
</style>

<div class="modal-header">
  <h4 class="modal-title"><?php 
       if(empty($quiz)){
         echo "Add Quiz";  
       }else{
         echo "Update Quiz";
       }
       ?></h4>
  <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<!-- Modal body -->
<div class="modal-body">
  @if(!empty($quiz))  
    <form action="{{url('/api/v2/add_update_quiz_table')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/quiz')}}'>
  @else
    <form action="{{url('/api/v2/add_update_quiz_table')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/quiz')}}'>
  @endif

      @if(!empty($quiz))
      <input type="hidden" name="id" value="<?= $quiz['id']; ?>"/>
      @endif
      <input type='hidden' name='role' value="<?= $request['role']; ?>">
      @if(Auth::guard('admin')->check())
        <label for="class">School<span class="start">*</span></label>
        @if(!empty($quiz))
          <select class="form-control" name='school_id'>
            @foreach($schools as $school)
              @if($quiz['school_id'] == $school->id)
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
          <div class="row">
              <div class="col-md-12">
                  <label for="class">Class-Section<span class="start">*</span></label>
              </div>
              <div class="col-md-12 parent-select">
                  
                    <select class="form-control" name="class_section_id" id="class_section_id" onchange="getALLSubjectsByClass(this.value)">
            <option value="">Select type</option>
            @foreach($class_sections as $class_section)
            @if(array_key_exists("class_section_id",$quiz))
            <option {{($quiz['class_section_id'] === $class_section->id ? 'selected':'' ) }} value="{{$class_section->id}}">{{ucfirst($class_section->class_name_section_name)}}</option>
            @else
            <option value="{{$class_section->id}}">{{ucfirst($class_section->class_name_section_name)}}</option>
            @endif
            @endforeach
        </select>
              </div>
          </div>
          <div class="row mt-2">
              <div class="col-md-12"> <label for="class">Subject<span class="start">*</span></label></div>
              <div class="col-md-12 parent-select">
                   <div class="form-group">
        
          <select class="form-control" name="subject_id" id="subject_id">
            <option value="">Select Subject</option>
            
        </select>
              </div>
          </div>
         
        
      </div>

     
      </div>

      <div class="form-group">
        <label for="section">Title <span class="start">*</span></label>
        <input type="text" name="name" class="form-control" value="{{(array_key_exists("name",$quiz)?$quiz['name']:'') }}" placeholder="Enter quiz title" id="name">
      </div>
      <button type="submit" class="btn btn-primary float-right submitButton">Save</button>

    </form>
</div>

<script>
     $('#start_time').datetimepicker({
        startDate: false,
        step: 15
     });
     $('#expired_time').datetimepicker({
        startDate: false,
        step: 15
     });
  $('select').SumoSelect({search: true});
</script>