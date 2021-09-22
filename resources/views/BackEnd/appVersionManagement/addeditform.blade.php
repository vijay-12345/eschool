<!-- Modal Header -->
<?php 
   error_reporting(0);
   ?>
<div class="modal-header">
   <h4 class="modal-title">
      <?php 
         if(empty($appVersion)){
           echo "Add App Version";  
         }else{
           echo "Update App Version";
         }
   
         
         ?>
   </h4>
   <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<!-- Modal body -->
<div class="modal-body" >
<form action="{{url('/api/v2/add_update_app_version')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/app-version')}}'>
   <?php   if(!empty($appVersion)){ ?>
   <input type="hidden" name="id" value="<?= $appVersion['id']; ?>"/>
   <?php }?>
   <input type='hidden' name='role' value="<?= $request['role']; ?>">
   <div class="form-group">
      <label for="class">School <span class="start">*</span></label>
      <select class="form-control" name="school_id">
      <option value="">Select school</option>
      @if(!empty($schools))
      @foreach($schools as $school)
      @if(array_key_exists("school_id",$appVersion))
      <option {{($appVersion['school_id'] == $school->id ? 'selected':'' ) }} value="<?= $school->id; ?>"><?= $school->name; ?></option>
      @else
      <option value="<?= $school->id; ?>"><?= $school->name; ?></option>
      @endif
      @endforeach
      @endif
   </select>
   </div>
   <div class="form-group">
      <label for="section">Version <span class="start">*</span></label>
      <input type="text" name="app_version" id="section" class="form-control" placeholder="Enter app version" value="<?= $appVersion['app_version']; ?>">
   </div>
   <div class="form-group">
      <label for="section">Status </label>
      <select class="form-control" name="mandatory_status" id="class_teacher">
         <option value="">Select status</option>
         @if(!empty($status))
         @foreach($status as $st)
         <option <?= ($st === $appVersion['mandatory_status'] ? 'selected':'' ) ?> value="<?= $st; ?>"><?= $st; ?></option>
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