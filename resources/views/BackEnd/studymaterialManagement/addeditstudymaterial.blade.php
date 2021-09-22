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
            <div class="modal-body" id="popbody">
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
                <label>Class-Section and Subject<span class="start">*</span></label>
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
            
            
            <?php $today=date('Y-m-d'); ?>
            <input type='hidden' name='date' value="<?= $today; ?>">

            <div class="form-group" style="margin-bottom: 1em;">
              <label for="section">Title <span class="start">*</span></label>
              <input type="text" name="title" class="form-control"  placeholder="Enter Title " id="title" value="{{(array_key_exists("title",$studymaterial)?$studymaterial['title']:'') }}">
            </div>

            <div class="form-group">
              <label for="section"> Url</label>
              <input type="text" name="content" class="form-control"  placeholder="Enter Url " id="content" value="{{(array_key_exists("content",$studymaterial)?$studymaterial['content']:'') }}">
            </div>

            
                <div id="row2" class="dynamic-added">
                    <input name="image[]" type="file" style="border: 1px solid black;width:100%;" id="attachment" multiple>
                    
                    <input name="file_label[]" style="border: 1px solid black; width:100%;height:40px;margin-bottom:20px;margin-top:5px;" id="file_title" placeholder="Enter File Title" type="text">
                </div>

            @if(!empty($studymaterial))
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
            

            

            
<!--             <button type="button" id="AddButtn" class="btn btn-primary" style="margin-left:170px">Add More Attachment</button> -->
         
            <div id="mainAttach">
                
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
    });

   $(document).ready(
    function()
    {
        var count = 0;
        var i=1;
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