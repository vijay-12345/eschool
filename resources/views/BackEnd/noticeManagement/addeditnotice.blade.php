<!-- Modal Header -->


            <div class="modal-header">
              <h4 class="modal-title"><?php 
              
         if(empty($notice)){
           echo "Add Notice";  
         }else{
           echo "Update Notice";
         }
         ?></h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
            @if(!empty($notice))  
            <form action="{{url('/api/v2/update_notice_board')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/notice')}}'>
            @else
            <form action="{{url('/api/v2/add_notice_board')}}" method="POST" class="eschoolForm" enctype="multipart/form-data" rel='{{url($request['role'].'/notice')}}'>
            @endif  
            @if(!empty($notice))
            <input type="hidden" name="id" value="<?= $notice['id']; ?>"/>
            @endif
            <input type='hidden' name='role' value="<?= $request['role']; ?>">
            @if(Auth::guard('admin')->check())
              <label for="class">School<span class="start">*</span></label>
              @if(!empty($notice))
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
                 <label for="class">Type <span class="start">*</span></label>
                <select class="form-control" name="type">
                    <option value="">Select type</option>
                    @foreach($type as $typ)
                    @if(array_key_exists("type",$notice))
                    <option {{($notice['type'] === $typ ? 'selected':'' ) }} value="{{$typ}}">{{ucfirst($typ)}}</option>
                    @else
                    <option value="{{$typ}}">{{ucfirst($typ)}}</option>
                    @endif
                    @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="section">Title <span class="start">*</span></label>
                <input type="text" name="title" class="form-control" value="{{(array_key_exists("title",$notice)?$notice['title']:'') }}" placeholder="Enter title name" id="title">
              </div>
              <div class="form-group">
                  <label>Message <span class="start">*</span></label>
                  <textarea rows="4" cols="80" class="form-control" name="message" placeholder="Enter message" >{{(array_key_exists("message",$notice)?$notice['message']:'') }}</textarea>
              </div>
              <button type="submit" class="btn btn-primary float-right submitButton">Save</button>
            </form>
            </div>
            <script>
  $('select').SumoSelect({search: true});
</script>