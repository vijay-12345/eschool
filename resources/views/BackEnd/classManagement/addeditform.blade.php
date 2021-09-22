<!-- Modal Header -->
<?php 
   error_reporting(0);
   ?>
<div class="modal-header">
   <h4 class="modal-title">
      <?php 
         if(empty($class_section)){
           echo "Add Class";  
         }else{
           echo "Update Class";
         }
         ?>
   </h4>
   <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<!-- Modal body -->
<div class="modal-body" >
   <?php   if(!empty($class_section)){ ?>
   <form action="{{url('/api/v2/update_class_section')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/class')}}'>
   <input type="hidden" name="id" value="<?= $class_section->id; ?>"/>
   <?php }else{ ?>
     <form action="{{url('/api/v2/add_class_section')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/class')}}'>
  <?php } ?>
   <input type='hidden' name='role' value="<?= $request['role']; ?>">
   @if(Auth::guard('admin')->check())
      <label for="class">School<span class="start">*</span></label>
        @if(!empty($class_section))
          <select class="form-control" name='school_id'>
            @foreach($schools as $school)
              @if($class_section->school_id == $school->id)
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
      <label for="class">Class <span class="start">*</span></label>
      <input type="text" name="class_name" id="class" class="form-control" placeholder="Enter class name" value="{{$class_section->class_name}}">
   </div>
   <div class="form-group">
      <label for="section">Section <span class="start">*</span></label>
      <input type="text" name="section_name" id="section" class="form-control" placeholder="Enter section name" value="{{$class_section->section_name}}">
   </div>
   <div class="form-group">
      <label for="section">Class Teacher </label>
      <select class="form-control" name="class_teacher_id" id="class_teacher">
         <option value="">Select class and section</option>
         @if(!empty($teachers))
         @foreach($teachers as $teacher)
         <option <?= ($teacher->id === $class_section->class_teacher_id ? 'selected':'' ) ?> value="<?= $teacher->id; ?>"><?= $teacher->name ?></option>
         @endforeach
         @endif
      </select>
   </div>
   <button class="btn btn-primary float-right submitButton">Save</button>
   </form>
</div>
<script>
   $('select').SumoSelect({search: true});
</script>