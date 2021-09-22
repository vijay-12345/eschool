@extends('BackEnd/layouts.master')
@section('title')
Quiz Result Management | School App
@endsection
@section('content')
<div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
                <div class="card-header d-sm-flex justify-content-between">
                <h4 class="card-title">{{ $quiz_section_subject->class_section_name }} || {{ $quiz_section_subject->subject_name }} || {{ $quiz_section_subject->name }}</h4>
                <div>
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url(session("role")."/quiz")}}">Quiz</a></li>
                        <li class="breadcrumb-item"><a href="{{url(session("role")."/quiz-detail/addQuestion/".$quiz_section_subject->id)}}">Quiz Question</a></li>
                        <li class="breadcrumb-item"><a href="{{url(session("role")."/quiz-results/".$quiz_section_subject->id)}}">Question Result</a></li>
                    </ol>
                  </nav>
                </div>
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
                <div class="card-body text-left">
                 
                </div>

                <div class="table-responsive">
                <table id="dataTable" class="display">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Roll No</th>
                                <th>Total Quiz Time</th>
                                <th>Time Taken</th>
                                <th>Correct Answer</th>
                                <th>Percentage</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                          @if(!empty($quiz_results))
                          @foreach($quiz_results as $quiz_result)
                            <tr>
                                @foreach($students as $student)
                                  @if($student->id == $quiz_result->user_id)
                                  <td>{{ucfirst($student->name)}}</td>
                                  <td>
                                    @if($student->profile_pic_url !='')
                                      <img src="{{$student->profile_pic_url}}" alt="logo" width="50">                          
                                      @else
                                         <span class="glyphicon glyphicon-user"></span>
                                      @endif              
                                  </td>
                                  <td>{{ucfirst($student->roll_no)}}</td>
                                  @endif
                                @endforeach
                                @foreach($quiz_tables as $quiz_table)
                                  @if($quiz_table->id == $quiz_result->quiz_table_id)
                                  <td>{{ucfirst($quiz_table->total_time)}}</td>
                                  @endif
                                @endforeach
                                <td>{{ucfirst($quiz_result->time_elapsed)}}</td>
                                <td>{{ucfirst($quiz_result->correct_count.'/'.$quiz_result->total_question)}}</td>
                                <td>{{number_format((float)$quiz_result->result, 2, '.', '')}}</td>
                                
                                <?php $time = Carbon\Carbon::parse($quiz_result->attempted_date); ?>
                                <?php $time_month = $time->format('m'); ?>
                                <?php $timeObj   = DateTime::createFromFormat('!m', $time_month); ?>
                                <?php $time_monthName = $timeObj->format('F') ?>
                                <td>{{$time->format('d').' '.$time_monthName.', '.$time->format('Y').'       '  .$time->format('h:ia')}}</td>
                            </tr>
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