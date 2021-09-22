<!-- Modal Header -->


<div class="modal-header">
  <h4 class="modal-title d-flex justify-content-between align-item-end w-100" ><?php 
  echo "Add Question";  ?> <span id="addother" style="color:green; text-align: right; font-size: 17px; display:none;">You can add other question</span></h4>
  <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<!-- Modal body -->
<div class="modal-body">
    <form action="{{url('/api/v2/add_update_quiz_detail_table')}}" id="addQuestionres" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url(session("role").'/quiz-detail/addQuestion/'.$quize_id)}}'>
      <input type='hidden' name='role' value="<?= $request['role']; ?>">
      <input type='hidden' name='quiz_table_id' value="{{ $quize_id }}">

      <div class="form-group">
        <label for="question">Question <span class="start">*</span></label>
        <textarea name="question" class="form-control" value="" placeholder="Enter Question" id="question"></textarea>
      </div>

      <div class="form-group">
        <label for="option_A">Option A<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_A" name="option_A" placeholder="Enter Option A"></textarea>
      </div>

      <div class="form-group">
        <label for="option_B">Option B<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_B" name="option_B" placeholder="Enter Option B"></textarea>
      </div>
  
      <div class="form-group">
        <label for="option_C">Option C<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_C" name="option_C" placeholder="Enter Option C"></textarea>
      </div>

      <div class="form-group">
        <label for="option_D">Option D<span class="start">*</span></label>
        <textarea class="form-control" value="" id="option_D" name="option_D" placeholder="Enter Option D"></textarea>
      </div>

      <div class="form-group">
        <label for="correct_answer">Correct Answer<span class="start">*</span></label>
        <select name="correct_answer" id="correct_answer" class="correct_answer">
          <option value="">Select type</option>
          <option value="A">{{ucfirst("A")}}</option>
          <option value="B">{{ucfirst("B")}}</option>
          <option value="C">{{ucfirst("C")}}</option>
          <option value="D">{{ucfirst("D")}}</option>
        </select>
      </div>
      <div class="row">
      <div class="col-md-5">
          <!---<a  class="btn btn-success" rel='/api/v2/quiz-publish-form' href='/api/v2/quiz-publish-form'  >
            Publish
          </a>-->
         <button  class="btn btn-primary questionSubmitButton">Save</button>
      </div>
      <div class="col-md-2">
      
      </div>
      <div class="col-md-5">
         <button  class="btn btn-primary questionSubmitMoreButton">Save & Add More</button>
         <!---<button type="button"  class="btn btn-primary testSubmitMoreButton">Save & Add More..</button>--->
      </div>
      
      </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('body').on('click', '.testSubmitMoreButton', function() {
            event.preventDefault();
            
            $('.correct_answer').SumoSelect();
            $('.correct_answer')[0].sumo.selectItem("0");
            
           //$('select.SlectBox')[0].sumo.unSelectAll();
           //$('#correct_answer').val('');
//    
//    
//            
//            var indexVal=$("#correct_answer").index();
//            alert(indexVal); 
//              $("#correct_answer").val('');
//    $("#correct_answer").find("option:selected").removeAttr("selected");
//    alert($("#correct_answer").html());
//             $('#correct_answer')[0].sumo.unload();
//$('#correct_answer').val('');
//            
//            $(".correct_answer option:selected").prop('selected' , false)
//            
//            $("#addQuestionres").find('input:text, select, textarea').val('');
//            $('form#correct_answer select').trigger("change"); //Line2
//
//$('#correct_answer').val('');
//$('#correct_answer').prop("selected", false);
//
//        $('#correct_answer').removeAttr('selected').find('option:first').attr('selected', 'selected').trigger("change"); 
//        
//        
//       
        
        alert('testing');
        });
       
    });
  $('select').SumoSelect({search: true});
</script>