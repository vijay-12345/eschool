@extends('BackEnd/teacherLayouts.master')
@section('title')
Forum Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Forum Management</h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif
              <div class="card-body text-right">
                <div class="card-body text-left" style="margin-right:20%;margin-left:20%;">
                          @if(!empty($datas))
                              @foreach($datas as $key=>$data)
                                <div style="margin-bottom:30px;background-color:#F0F8FF;font-size:120%">
                                  <b>
                                  <div class="box">
                                    <div class="row" style="padding-left:20px;padding-bottom:20px;padding-top:5px;">    
                                        @if($data->who_replyed == 'student')
                                          @foreach($students as $student)
                                            @if($student->id == $data->reply_from)
                                              
                                                @if($student->isProfilePic == '1')
                                                  <img src="{{$student->profile_pic_url}}" alt="logo" width="50" height="50" style="border-radius: 100%;">                          
                                                @elseif($student->isProfilePic == '0')
                                                   <span class="glyphicon glyphicon-user"></span>
                                                @endif
                                              
                                              
                                            @endif
                                          @endforeach
                                        @else
                                          @foreach($teachers as $teacher)
                                            @if($teacher->id == $data->reply_from)
    
                                                @if($teacher->isProfilePic == '1')
                                                  <img src="{{$teacher->profile_pic_url}}" alt="logo" width="50" height="50" style="border-radius: 100%;">                          
                                                @elseif($teacher->isProfilePic == '0')
                                                   <span class="glyphicon glyphicon-user"></span>
                                                @endif
                                              
                                              
                                             
                                            @endif
                                          @endforeach
                                        @endif
                                       
                                       <div class="col-md-8">
                                          @if($data->who_replyed == 'student')
                                            @foreach($students as $student)
                                              @if($student->id == $data->reply_from)
                                                
                                                  {{ucfirst($student->name)}}
                                                
                                              @endif
                                            @endforeach
                                          @else
                                            @foreach($teachers as $teacher)
                                              @if($teacher->id == $data->reply_from)
                                              
                                              {{ucfirst($teacher->name)}}
                                               
                                              @endif
                                            @endforeach
                                          @endif
                                          <?php echo '('; ?>
                                          {{$data->who_replyed}} 
                                          <?php echo ')'; ?></b></br>
    
                                          <?php $date = Carbon\Carbon::parse($data->date); ?>
                                          <?php $date_month = $date->format('m'); ?>
                                          <?php $dateObj   = DateTime::createFromFormat('!m', $date_month); ?>
                                          <?php $date_monthName = $dateObj->format('F') ?>
                                          {{$date->format('d').' '.$date_monthName.', '.$date->format('Y').'       '  .$date->format('h:ia')}}
                                        </div>
                                        <div class="col-md-3">
                                          @foreach($attachments as $attachment)
                                              @if($attachment->reference_id == $data->id)
                                                
                                                <?php
                                                $ext='';
                                                $info = pathinfo($attachment->file_url);
                                                $ext = $info['extension'];
                                                ?>
                                                <a  href="{{url(session('role').'/library-assignment/download/'.$attachment->id)}}"  style="font-size:120%;">
                                                <span class="glyphicon glyphicon-download"  ></span> Download</a> </br>  
                                                
                                              @endif
                                          @endforeach
                                        </div>
                                    </div>
                                  </div>
                                  
                                  @foreach($attachments as $attachment)
                                      @if($attachment->reference_id == $data->id)
                                        <?php
                                        $ext='';
                                        $info = pathinfo($attachment->file_url);
                                        $ext = $info['extension'];
                                        ?>
    
                                        <?php  if($ext == 'jpg' or $ext == 'jpeg' or $ext == 'png'){ ?>
                                        <img src="<?=$attachment->file_url;?>" width="100%" height="250"> 
                                        <?php }else{ ?>
                                        <img src="<?php echo url('/') ?>/adminAssets/img/pdf.png" style="width:70%;height:32%;">
                                        <?php } ?>   
                                        </br>
    
                                      @endif
                                  @endforeach
                                        
                                  <div style="padding-top:10px;padding-left:10px;">{{$data->message_content}}</br>
                                  </div>
                                     
                                </div>
                                @endforeach
                            @endif
                            <div style="margin-top:70px;">
                              WRITE REPLY 
                                <div style="margin-bottom:30px;background-color:#FAEBD7; font-size:120%">
                                  <b>
                                  <div class="box">
                                    <div class="row" style="padding-left:20px;padding-bottom:20px;padding-top:5px;">  
                                    </div>
                                    <form action="{{url('/api/v2/add_forum_post_web_panel')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/forum/'.$teacher_class_subject['id'].'/view')}}'>
                                        <input type='hidden' name='role' value="<?= $request['role']; ?>">
                                        <input type='hidden' name='school_id' value="<?= $request['school_id']; ?>">
                                        <input type='hidden' name='date' value="{{date("Y/m/d")}}">
                                        <input type='hidden' name='teacher_class_subject_id' value="{{$teacher_class_subject['id']}}">
                                        <input type='hidden' name='reply_from' value="{{$request['teacher_id']}}">
                                        <input type='hidden' name='who_replyed' value="teacher">
                                        <div class="form-group">
                                            <label>Message <span class="start">*</span></label>
                                            <textarea rows="4" cols="80" class="form-control" name="message_content" placeholder="Enter message" ></textarea>
                                        </div>
                                        <div class="form-group">
                                              <label>Select Pic <span class="start"></span></label>
                                              <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="image[]" id="pic">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                              </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary float-right submitButton">Reply</button>
                                        </form>
                                        </div>
                              </div>
                          </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal" id="addInForm">
        <div class="modal-dialog">
          <div class="modal-content" id = "addinform">
          </div>
        </div>
      </div>

@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    $('#dataTable').DataTable();
    });
    $('#pic').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
    $(document).ready(function() {
       $('html,body').animate({scrollTop: document.body.scrollHeight},"slow");
    })

</script>
@endsection