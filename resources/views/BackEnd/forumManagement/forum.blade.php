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
            

              
                <div class="table-responsive">
                        <table id="dataTable" class="display">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Subject And Class Name</th>
                                        <th>Actions</th>
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php $id=1; ?>
        
                                  @if(!empty($teacher_class_subjects))
                                  @foreach($teacher_class_subjects as $teacher_class_subject)
                                    <tr>
                                        <td><?= $id; ?></td>
                                        <td>{{ucfirst($teacher_class_subject['class_name'].'-'.$teacher_class_subject['section_name'].' ,'.$teacher_class_subject['subject_name'])}}</td>
                                        <td>
                                            <a href="{{url(session("role").'/forum',[$teacher_class_subject['id'],'view'])}}" class="btn btn-primary">Forum</a>
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




@endsection
@section('scripts')
<script>
    $(document).ready( function () {
    $('#dataTable').DataTable();
} );

</script>
@endsection