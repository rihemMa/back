<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\models\PaperType;
use App\models\Paper;
use App\models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class PaperController extends Controller
{

    // create new paper By admin
    public function create(Request $request)

    {
        
        $user_id = Auth::user()->id;
        $paper = new Paper($request->paper);
        $paper->paper_file = $request->file_path ; 
        $paper->start_date = $paper->start_date->addHour();
        $paper->end_date = $paper->end_date->addHour();
        $paper->alert_date = $paper->end_date->subMonth();
        $paper->save();

        $activity = new ActivityLog();
        $activity->logSaver($user_id,'create','paper',$paper->id);
        return response()->json(['message'=>'created']) ;
    }




    // update paper by user&admin
    public function update(Request $request)
    {
        
        
        $user_id = Auth::user()->id;
        $id = $request->paper_id;
        $paper = Paper::find($id) ;
        $paper->update($request->newPaper);
        if($request->file_path){ 
        $paper->paper_file = $request->file_path ;}
        $paper->save();
        $activity = new ActivityLog();
        $activity->logSaver($user_id,'update','paper',$paper->id);
        return response()->json('updated') ;
    }



    //get all papers for admin
    public function getAllPapers()
    {
        $papers = Paper::with('paperType','project')->get();
        return $papers ;
    }


    // get Paper By id
    public function getPaperById($id)
    {
        $paper = Paper::find($id);
        if(is_null($paper))
        {
            return response()->json(["message"=>"Not found"]);
        }
        return $paper ;
    }


    // delete paper by admin
    public function delete(Request $request)

    {   $user_id = Auth::user()->id;
        $table = $request->papers_id;


        foreach ($table as $t)
        {
            $id= ($t['paper_id']);
            $paper = Paper::find($id);
            $paper->delete() ;

        

            $activity = new ActivityLog();
            $activity->logSaver($user_id,'delete','paper',$paper->id);

    }
    return response()->json(['message'=>'Deleted']) ;

    }
//get type of the paper
    public function getTypeofThePaper($id)
    {
        $paper = Paper::where('id',$id)->with('paperType')->get();
        return response($paper);
    }

    public function uploadFile(Request $request)
    { 
            $file = $request->file('file');
            $ex = $file->getClientOriginalExtension();
            $file_name = time().'.'.$ex ;
            $file_path ='files/images' ;
            $file->move($file_path,$file_name);
            $path = $file_path.'/'.$file_name;
            return response()->json(["path"=>$path]) ;
    }


    


    
// get just contracts
   public function getJustContracts()
    {
     $contracts  = array(); 
     $update  = array(); 
     $hosting  = array(); 
     $maintenance  = array(); 
     $today = Carbon::today();
     /*  , function($query){

        $query->

    } */
    $papers = Paper::with('type.email')->with('project.client')->where('alert_date','<=',$today)->get() ;
    
        foreach($papers  as $t){
            if(!($t->type == NULL)){
                array_push($contracts,$t) ;
            }
        }
        foreach($contracts as $tab){
            if($tab->type['paper_type']  == "update"){
                array_push($update,$tab) ;
            }elseif($tab->type['paper_type'] == "hosting"){
                array_push($hosting,$tab) ;
            }elseif($tab->type['paper_type']=="maintenance"){
                array_push($maintenance,$tab);
            }
        }
     
      return response()->json(["contracts"=>$contracts,"hosting"=>$hosting,"update"=>$update,"maintenance"=>$maintenance]);
    }
    
   


  public function sendMail(Request $request)
     {
                $contracts  = $request->contracts ;
              foreach ( $contracts as $cont)
              {
                  
                $to_name  = $cont['project']['client']['client_name'];
                $to_email = $cont['project']['client']['email'];
                $subject = $cont['type']['email']['subject'];

                $emailBody = $cont['type']['email']['content'] ;
                $emailBody = str_replace("{client_name}",$cont['project']['client']['client_name'],$emailBody) ;
                $data = array('name'=> $cont['project']['client']['client_name'] ,'body' =>$emailBody );
                Mail::send('alertEmail', $data, function($message) use ($to_name, $to_email,$subject) {
                $message->to($to_email, $to_name)->subject($subject);
                $message->from('dossaziz18@gmail.com','Nachd-it');
                });
                  $id = $cont['id'] ;
                  $contract = Paper::find($id);
                  $contract->isReminded =  1;
                  $contract->save();

              }
                return response()->json(["msg"=>"mail sent"]);
     }




     public function changeStatus(Request $request){
         $papers =$request->papers ;
         $status_id = $request->status_id ;
         foreach( $papers as $paper)
         {
             $p = Paper::find($paper['id']);
             $p->status = $status_id ;
             $p->save() ;
         }

         return response()->json(["msg"=>" status updated"]);
     }
 


     public function getExpiredContracts()
     {
         $contracts  = array(); 
         $today = Carbon::today();
         $papers = Paper::with('type')->where('end_date','<=',$today)->get();
         foreach($papers  as $t){
            if(!($t->type == NULL)){
                array_push($contracts,$t) ;
            }
        }
         return $contracts ;
     }


 
}

