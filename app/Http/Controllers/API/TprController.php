<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tpr;

use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use Mail;//for default mail service in laravel
use Illuminate\Support\Str;//for generating random string
use Illuminate\Support\Facades\URL;//for generating dynamic url
use Illuminate\Support\Carbon;//for geting current datetime
use App\models\PasswordReset;
class TprController extends Controller
{
    
//mail sent for forget password
public function forgetPassword(Request $req){
    try{
        $user=Tpr::where('email',$req->email)->get();
        if(count($user)>0){
        $token=Str::random(40);
        $domain=URL::to('/');
        $url=$domain.'/tpr/reset-password?token='.$token;
        $data['url']=$url;
        $data['email']=$req->email;
        $data['title']='Forget password';
        $data['body']='please click on below link to reset your password';
        Mail::send('forgetPassMail',['data'=>$data],function($msg)use($data){
           $msg->to($data['email'])->subject($data['title']);
        
        });
        $datatime=Carbon::now()->format('Y-m-d H:i:s');
        PasswordReset::updateOrCreate(
            ['email'=>$req->email],//conditions
        
            [
            'token'=>$token,
            'email'=>$req->email,
            'created_at'=>$datatime
            ],
        );
        return response()->json(['success'=>true,'email'=>$req->email,'msg'=>'please check your mail to reset your password']);
        
        
        }else{
            return response()->json(['success'=>false,'email'=>$req->email,'msg'=>'user not found']);
        }
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        
        }
}


    ///reset password view load
    public function resetPasswordLoad(Request $req){
        $resetdata= PasswordReset::where('token',$req->token)->get();  //print_r($resetdata[0]['email']);
 
       // print_r($resetdata[0]);
        if(isset($req->token) && count($resetdata)>0){
 
         $user=Tpr::where('email',$resetdata[0]['email'])->get();
       
       // print_r($user[0]['email']);
         return view('resetPassword',compact('user'));
        }else{
         return view('404');
 
        }
     }

        //password reset functionality
    public function resetPassword(Request $req){
        $req->validate([
            // 'password'=>'required|string|min:6|confirmed'
            'password'=>[
                'required',
                'confirmed',
                Password::min(6)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols()
           
            ]
        ]);
        $user=Tpr::find($req->id);
        $user->password=Hash::make($req->password);
        $user->save();
        PasswordReset::where('email',$user->email)->delete();
        return "<h1>Your password has been updated successfully!</h1>";
            }

}
