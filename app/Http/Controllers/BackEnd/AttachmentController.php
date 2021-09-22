<?php
namespace App\Http\Controllers\BackEnd;
use Illuminate\Http\Request;
use \Validator;
use Auth;
use App\Attachments_Table;
use DB;


class AttachmentController extends \App\Http\Controllers\Controller
{
    public function deleteRecord(Request $request,$id_par)
    {
            $objAttachments_Table = new Attachments_Table();
            $id= $objAttachments_Table->deleteRecord($id_par);

            if(!empty($id)){
                    return response()->json([
                        'success' => 'Record deleted successfully!',
                        'id'=>$id
                    ]);
            } 
            return null;          
        
    }

}
