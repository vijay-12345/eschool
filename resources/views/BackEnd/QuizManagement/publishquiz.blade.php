<!-- Modal Header -->


<div class="modal-header">
  <h4 class="modal-title"><?php 
         echo "Publish Quiz";  
       ?></h4>
  <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<!-- Modal body -->
<div class="modal-body">
  @if(!empty($quiz))  
    <form action="{{url('/api/v2/add_publish_quiz_table')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/quiz')}}'>
  @else
    <form action="{{url('/api/v2/add_publish_quiz_table')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/quiz')}}'>
  @endif

      <input type="hidden" name="id" value="<?= $quize_id; ?>"/>
      <input type="hidden" name="publish" value="1"/>
      <input type='hidden' name='role' value="<?= $request['role']; ?>">
      <div class="form-group">
        <label for="start_time">Start Time<span class="start"></span></label>
        <input type="text" class="form-control" value="" id="start_time" name="start_time">
      </div>
      <div class="form-group">
        <label for="expired_time">Expired Time<span class="start">*</span></label>
        <input type="text" class="form-control" value="" id="expired_time" name="expired_time">
      </div>
      
      <div class="form-group">
        <label for="total_time">Total Time(in hh:mm)<span class="start">*</span></label>
        <input type="text" class="form-control" value="" id="total_time" name="total_time" placeholder="00:00">
      </div>

      <button type="submit" class="btn btn-primary float-right submitButton">Publish</button>

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