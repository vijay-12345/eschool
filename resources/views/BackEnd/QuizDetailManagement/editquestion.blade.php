<!-- Modal Header -->
<?php
if($quiz_detail->correct_answer == $quiz_detail->option_A){
    $correctAnswer="A";
}elseif($quiz_detail->correct_answer == $quiz_detail->option_B){
     $correctAnswer="B";
}
elseif($quiz_detail->correct_answer == $quiz_detail->option_C){
     $correctAnswer="C";
}
elseif($quiz_detail->correct_answer == $quiz_detail->option_D){
     $correctAnswer="D";
}
else{
     $correctAnswer="";
}

?>

<div class="modal-header">
  <h4 class="modal-title">Update Question</h4>
  <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<!-- Modal body -->
<div class="modal-body">
    <form action="{{url('/api/v2/add_update_quiz_detail_table')}}" id="editQuestion" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url(session("role").'/quiz-detail/addQuestion/'.$quiz_detail['quiz_table_id'])}}'>
     <input type="hidden" name="id" id='id' value="<?= $quiz_detail['id']; ?>">
     <input type="hidden" name="editId" id='editId' value="<?= $quiz_detail['id']; ?>">
      <input type='hidden' name='role' value="<?= $request['role']; ?>">
      <!---<div class="form-group">
         <label for="class">Quiz<span class="start">*</span></label>
          <select class="form-control" name="quiz_table_id" id="quiz_table_id">
            <option value="">Select Quiz</option>
            @foreach($quizs as $quiz)
                <option value="{{$quiz->id}}" {{($quiz->id == $quiz_detail['quiz_table_id']) ? 'selected' : ''}} >{{ucfirst($quiz->name)}}</option>
            @endforeach
        </select>
      </div>-->

      <div class="form-group">
        <label for="question">Question <span class="start">*</span></label>
        <textarea name="question" class="form-control" value="" placeholder="Enter Question" id="question"><?= $quiz_detail['question']; ?></textarea>
      </div>

      <div class="form-group">
        <label for="option_A">Option A<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_A" name="option_A" placeholder="Enter Option A">{{$quiz_detail['option_A']}}</textarea>
      </div>

      <div class="form-group">
        <label for="option_B">Option B<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_B" name="option_B" placeholder="Enter Option B">{{$quiz_detail['option_B']}}</textarea>
      </div>
  
      <div class="form-group">
        <label for="option_C">Option C<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_C" name="option_C" placeholder="Enter Option C">{{$quiz_detail['option_C']}}</textarea>
      </div>

      <div class="form-group">
        <label for="option_D">Option D<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_D" name="option_D" placeholder="Enter Option D">{{$quiz_detail['option_D']}}</textarea>
      </div>
      <div class="form-group">
        <label for="correct_answer">Correct Answer<span class="start">*</span></label>
        <select name="correct_answer" id="correct_answer">
          <option value="">Select type</option>
          <option value="A" {{($correctAnswer == 'A') ? 'selected' : ''}} >{{ucfirst("A")}}</option>
          <option value="B" {{($correctAnswer == 'B') ? 'selected' : ''}} >{{ucfirst("B")}}</option>
          <option value="C" {{($correctAnswer == 'C') ? 'selected' : ''}} >{{ucfirst("C")}}</option>
          <option value="D" {{($correctAnswer == 'D') ? 'selected' : ''}} >{{ucfirst("D")}}</option>
        </select>
      </div>
      <div class="row">
      <div class="col-md-4">
       
      </div>
      <div class="col-md-4">
          <button type="submit"  class="btn btn-primary questionSubmitButton">Save</button>
      </div>
      <div class="col-md-4">
       
       </div>
      </div>
    </form>
</div>

<script>
  $('select').SumoSelect({search: true});
</script>