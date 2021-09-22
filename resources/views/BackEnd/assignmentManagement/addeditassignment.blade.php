<!-- Modal Header -->


            <div class="modal-header">
              <h4 class="modal-title"><?php 
              
         if(empty($assignment)){
           echo "Add Assignment";  
         }else{
           echo "Update Assignment";
         }
         ?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            @if(!empty($assignment))  
            <form action="{{url('/api/v2/update-assignment-web-panel')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/assignment')}}'>
            @else
            <form action="{{url('/api/v2/add-assignment-web-panel')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/assignment')}}'>
            @endif  
            @if(!empty($assignment))
            <input type="hidden" name="id" value="<?= $assignment['id']; ?>"/>
            @endif
            <input type='hidden' name='role' value="<?= $request['role']; ?>">
            
            <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
          

            <div class="form-group">
              <div>
                <label>Class-Section and Subject<span >*</span></label>
                <select class="form-control" name="teacher_class_subject_id" id="teacher_class_subject_id" >
                  <option value="">Select Class-Section and Subject</option>
                  
                  @foreach($teacher_class_subjects as $teacher_class_subject)
                    @if(array_key_exists("teacher_class_subject_id",$assignment))
                    <option {{($assignment['teacher_class_subject_id'] === $teacher_class_subject['id'] ? 'selected':'' ) }} value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                    @else
                    <option value="{{$teacher_class_subject['id']}}">{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            
          

            <div class="form-group" style="margin-bottom: 1em;">
              <label for="section">Title <span class="start">*</span></label>
              <input type="text" name="title" class="form-control"  placeholder="Enter Title " id="title" value="{{(array_key_exists("title",$assignment)?$assignment['title']:'') }}">
            </div>

            <div class="form-group">
              <label for="section">Description <span class="start">*</span></label>
              <input type="text" name="assignment_description" class="form-control"  placeholder="Enter Description" id="assignment_description" value="{{(array_key_exists("assignment_description",$assignment)?$assignment['assignment_description']:'') }}">
            </div>

            <div class="form-group">
              <div>
                  <label>Due Date <span class="start">*</span></label>
                  <div>
                      <input type='text' class="form-control" placeholder="Select Due Date"  name="due_date" id="datepicker1" value="{{(array_key_exists("due_date",$assignment)?$assignment['due_date']:'') }}">
                      
                  </div>
              </div> 
            </div>

            
            
            <div id="row2" class="dynamic-added">
                <input name="image[]" type="file" style="border: 1px solid black;width:100%;" id="attachment" multiple>
                <input name="file_label[]" style="border: 1px solid black; width:100%;height:40px;margin-bottom:20px;margin-top:5px;" id="file_title" placeholder="Enter File Title" type="text">
            </div>


            @if(!empty($assignment))
              @if(!empty($attachment))
                @foreach($attachment as $attach)
                    <div id="attach{{$attach['id']}}">
                    <input type="hidden"  id="id" value="{{ $attach['id'] }}">
                    <tr >
                        <?php
                        $ext='';
                        $info = pathinfo($attach['file_url']);
                        $ext = $info['extension'];
                        if($ext == 'jpg' or $ext == 'jpeg' or $ext == 'png'){ ?>
                        <td><img src="<?=$attach['file_url'];?>" style="width:7%;height:2%;"> </td>
                        <?php }else{ ?>
                        <td><img src="<?php echo url('/') ?>/adminAssets/img/pdf.png" style="width:7%;height:2%;"> </td>
                        <?php } ?>

                        <td>
                                <a  href="{{url(session("role").'/library-assignment/download/'.$attach['id'])}}"  style="font-size:60%;" class="btn btn-primary">Download
                                    
                                </a> 
                                <button class="deleteRecord123 btn btn-primary"  data-id="{{ $attach['id'] }}" style="font-size:60%;">Delete</button> 

                        </td>
                        <br>
                        
                    </tr>
                  </div>
                    @endforeach
              @endif  
            @endif


          <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">            

            <!-- <button type="button" id="AddButtn" class="btn btn-primary" style="margin-left:170px">Add More Attachment<span class="start">*</span></button> -->
         
            <div id="mainAttach">
                
            </div>


           
            <button type="submit" class="btn btn-primary float-right submitButton">Save</button>
            </form>
            </div>



<script type="text/javascript">
        $(function () {
                $('#datepicker1').datepicker({
                  format: 'yyyy-mm-dd',
                   startDate: new Date(),
                   autoclose:true
                });
            });
        $('#attachment').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
        $(document).ready(
    function()
    {
        var count = 0;
        var i = 1;
        //i++; 
                //$('#mainAttach').append('<div id="row'+i+'" class="dynamic-added"><input name="image[]"  type="file" style="border: 1px solid black;width:100%;" id="attachment"><input name="file_label[]" style="border: 1px solid black; width:70%;height:50px;margin-bottom:20px;margin-top:5px;" id="file_title'+i+'" placeholder="Enter File Title" type="text"><button type="button" style="margin-left:10px;" name="remove" id="'+i+'" class="btn btn-danger btn_remove">Remove</button></div>');
        $('#AddButtn').click(
            function()
            {
                
              i++; 
                $('#mainAttach').append('<div id="row'+i+'" class="dynamic-added"><input name="image[]"  type="file" style="border: 1px solid black;width:100%;" id="attachment"><input name="file_label[]" style="border: 1px solid black; width:70%;height:50px;margin-bottom:20px;margin-top:5px;" id="file_title'+i+'" placeholder="Enter File Title" type="text"><button type="button" style="margin-left:10px;" name="remove" id="'+i+'" class="btn btn-danger btn_remove">Remove</button></div>');
            }
        );
        $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      });

      $(".deleteRecord123").click(function(){

    event.preventDefault();

    var id = $(this).data("id");

    var url = '{{url(session("role").'/delete_attachment/')}}'+'/'+id;
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
      type: "POST",
      url: url,
      data: { "id": id },
      processData: false,
      contentType: false,
      async: true,
      cache: false,
      timeout: 600000, 
            success: function(data) {
              $('#attach'+id).hide();
              console.log('#attach'+id);
            },
            error: function(e) {
                console.log("ERROR : ", e);
                hendleError(e.responseJSON);
            }
          });
    });


    });
    </script>