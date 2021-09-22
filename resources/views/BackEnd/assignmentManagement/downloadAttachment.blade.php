<!-- Modal Header -->


            <div class="modal-header">
              <h4 class="modal-title">Download Attachments</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <table>
                    @foreach($attachments as $attachment)
                    <input type="hidden" name="id" id="id" value="{{ $attachment->id }}">
                    <tr>
                        <?php
                        $ext='';
                        $info = pathinfo($attachment->file_url);
                        $ext = $info['extension'];
                        if($ext == 'jpg' or $ext == 'jpeg' or $ext == 'png'){ ?>
                        <td><img src="<?=$attachment->file_url;?>" style="width:70%;height:5%;"> </td>
                        <?php }else{ ?>
                        <td><img src="<?php echo url('/') ?>/adminAssets/img/pdf.png" style="width:70%;height:32%;"> </td>
                        <?php } ?>

                        <td><a  href="{{url(session("role").'/library-assignment/download/'.$attachment->id)}}"  style="font-size:150%;">
                                    <span class="glyphicon glyphicon-download" style="padding-right:3px" ></span> Download</a></td>
                        
                    </tr>
                    @endforeach
                </table>
            </div>