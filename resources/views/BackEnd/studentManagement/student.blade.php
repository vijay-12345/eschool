@extends('BackEnd/layouts.master')
@section('title')
 Student Management | School App
@endsection
@section('content')

<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title"> Student Management</h4>
              </div>
              @if(Session::has('success_message'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{Session::get('success_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif
              @if(Session::has('error_message'))
                     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{Session::get('error_message')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              @endif
            
              <form action="{{url(session("role").'/student')}}" method="POST"  enctype="multipart/form-data">
                @csrf  

                @if(Auth::guard('admin')->check())
                    <select class="form-control" name='school_id' id='school_id' onclick="getALLClassSection(this.value)" style="margin-left:20px;width:180px;">
                      <option value="">Select School</option>
                      @foreach($schools as $school)
                        @if(array_key_exists("school_id",$dropdown_selected))
                          @if($dropdown_selected['school_id'] == $school->id)
                            <option value="{{$school->id}}" selected>{{ucfirst($school->name)}}</option>
                          @else
                            <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                          @endif
                        @else
                           <option value="{{$school->id}}">{{ucfirst($school->name)}}</option>
                        @endif  
                      @endforeach
                    </select>  
                
                  @else
                  <input type='hidden' name='school_id' value="<?= session('user_school_id'); ?>">
                  @endif
                <select class="form-control"  name='class_section_id' id='class_section_id' style="margin-left:10px;width:180px;">
                      <option value="">Select Class_Section</option>
                      @if(!(Auth::guard('admin')->check()))
                        @foreach($class_sections as $class_section)
                            @if(array_key_exists("class_section",$dropdown_selected))
                              @if($dropdown_selected['class_section'] == $class_section->id)
                                <option value="{{$class_section->id}}" selected>{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                              @else
                                <option value="{{$class_section->id}}">{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                              @endif
                            @else
                              <option value="{{$class_section->id}}">{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                            @endif
                        @endforeach  
                      @else
                        @if(array_key_exists("class_section",$dropdown_selected))
                          @foreach($class_sections as $class_section)
                            @if($dropdown_selected['class_section'] == $class_section->id)
                                <option value="{{$class_section->id}}" selected>{{ucfirst($class_section->class_name.'-'.$class_section->section_name)}}</option>
                            @endif
                          @endforeach
                        @endif
                      @endif                
                        
                </select>
                  &nbsp; &nbsp;
                <button type="submit" class="btn btn-primary form-top" value="">Filter</button>
              </form> 

               <div style="float:right;">
                <a href="{{url(session("role").'/student/create')}}" class="btn btn-primary text-right" >Add Student</a>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importStudent">
                  Import Students
                </button>
                </div>             
               
                <div class="card-body text-right">
                <div class="table-responsive" cellspacing="0" width="100%">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Student Name</th>
                                <th>Registration No.</th>
                                <th>Login Id</th>
                                <th>Roll No.</th>
                                <th>Class Name</th>
                                <th>Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                          <?php $id=1; ?>
                          @if(!empty($students))
                          @foreach($students as $student)
                            <tr>
                                <td><?= $id; ?></td>
                                <td>{{$student->name}}</td>
                                <td>{{$student->registration_no}}</td>
                                <td>{{$student->login_id}}</td>
                                <td>{{$student->roll_no}}</td>
                                <td><?= $student->class_name.'-'.$student->section_name ?></td>
                                <td>
        <a href="{{url(session("role").'/student',[$student->id,'edit'])}}" class="btn btn-primary a-btn-slide-text">
        <i class="fa fa-edit"></i> Edit</span>            
        </a>
                                
    
<a href="javascript:;" class="btn btn-primary a-btn-slide-text" data-toggle="modal" onclick="deleteData({{$student->id}})" 
data-target="#DeleteModal" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                  </td>
    
                            </tr>
                            <?php $id++; ?>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                </div>
            
            </div>
          </div>
        </div>
      </div>

      <div class="modal" id="importStudent">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Import Students</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{url(session("role").'/import')}}" method="POST"  enctype="multipart/form-data">
            {{ csrf_field()}}
              <div class="form-group">
                  <label>Select excel file to upload <span class="start">*</span></label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" name="file" id="inputGroupFile02">
                    <label class="custom-file-label" for="customFile">Choose file</label>
                  </div>
              </div>
              <button type="submit" class="btn btn-primary float-left">Upload</button>
              <br>
              <a href="{{url('/sample/studentReg.xlsx')}}" class="float-right">Download Sample</a>
            </form>


            </div>

            <!-- Modal footer -->
           

          </div>
        </div>
      </div>
      <div id="DeleteModal" class="modal fade text-danger" role="dialog">
        <div class="modal-dialog ">
          <!-- Modal content-->
          <form action="" id="deleteForm" method="post">
              <div class="modal-content">
                  <div class="modal-header bg-danger">
                      <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                      <h4 class="modal-title text-center">DELETE CONFIRMATION</h4>
                  </div>
                  <div class="modal-body">
                      {{ csrf_field() }}
                      {{ method_field('DELETE') }}
                      <p class="text-center">Are You Sure Want To Delete ?</p>
                  </div>
                  <div class="modal-footer">
                      <center>
                          <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                          <button type="submit" name="" class="btn btn-danger" data-dismiss="modal" onclick="formSubmit()">Yes, Delete</button>
                      </center>
                  </div>
              </div>
          </form>
        </div>
        </div>
@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    $('#dataTable').DataTable();
} );
@if (count($errors) > 0)
    $('#addClass').modal('show');
    $('#importStudent').modal('show');
@endif
function deleteData(id)
{
    var id = id;
    var url = '{{url(session("role").'/student')}}';
    url = url+'/'+id;
    $("#deleteForm").attr('action', url);
}

function formSubmit()
{
    $("#deleteForm").submit();
}
$(document).ready(function() {
    $('#importStudent').on('hidden.bs.modal', function(){
      $('.custom-file-label').text('');
     });
     $('#inputGroupFile02').on('change',function(){
        //get the file name
        var fileName = $(this).val();
        //replace the "Choose a file" label
        $(this).next('.custom-file-label').html(fileName);
    })
});

function getALLClassSection(classSubId,getSubjectId=''){ 
        var url = '{{url(session("role").'/class_section-using-school')}}';
        $.ajax({
            type: "POST",
            url: url,
            data: {
                    "_token": "{{ csrf_token() }}",
                    "school_id":classSubId
                    },
            success: function(response){
                var jsonData = eval('(' + response + ')'); 
                  //localStorage.removeItem("model_checked");
                  $('#class_section_id').empty();
                  var modelContainerStr = '';
                  for(i=0; i < jsonData.length; i++){
                      var selected='';
                            if(jsonData[i].id == getSubjectId){
                                selected = 'selected';
                            }
                            modelContainerStr = modelContainerStr + '<option value="'+jsonData[i].id+'" '+selected+' >'+jsonData[i].class_name+'-'+jsonData[i].section_name+'</option>';        
                  } 
                  $('#class_section_id').append(modelContainerStr);
                  $('select#class_section_id')[0].sumo.reload();

          } 
          });
    }

</script>
@endsection