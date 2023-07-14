<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Hash;
use Mail;//for default mail service in laravel
use Illuminate\Support\Str;//for generating random string
use Illuminate\Support\Facades\URL;//for generating dynamic url
use Illuminate\Support\Carbon;//for geting current datetime
use App\models\PasswordReset;
class UserController extends Controller
{
    public function register(Request $req){
$validate=Validator::make($req->all(),[
    'name'=>'required|string|min:2|max:200',
'email'=>'required|string|email|max:100|unique:users',
'password'=>'required|string|min:6|confirmed'
]);
if($validate->fails()){
    return response()->json($validate->errors());
}

$user=User::create([
    'name'=>$req->name,
    'email'=>$req->email,
    'password'=>Hash::make($req->password),
]);


return response()->json([
    'msg'=>'User inserted successfully',
    'user'=>$user,
]);
    }



    public function login(Request $req){
        $validate=  Validator::make($req->all(),[
            'email'=>'required|string|email',
            'password'=>'required|min:6|string',
        ]);


        if($validate->fails())
        {
            return response()->json($validate->errors());
        }
        if(!$token=auth()->attempt($validate->validated())){//auth()->attempt ceck if email password combinatino exist or not and if exist in db then retun a token
return response()->json(['success'=>false,'msg'=>'invalid credential']);
        }
return $this->respondWithToken($token);

    }

    protected function respondWithToken($token){
return response()->json([
    'success'=>true,
    'access_token'=>$token,
    'token_type'=>'Bearer',
    'expires_in'=>auth()->factory()->getTTL()*60,
]);
    }
   



    public function logout(){

        try{  auth()->logout();
       
            return response()->json(['success'=>true,'msg'=>'user logged out!']);
        }catch(\Exception $e){
            return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
        }
      
    }

    public function profile(){
        
try{
return response()->json(['success'=>true,'data'=>auth()->user()]);
} catch(\Exception $e)
{
    return response()->json(['success'=>false,'msg'=>$e->getMessage()]);

}
    }

    public function updateProfile(Request $req){
if(auth()->user()){
    $validate=Validator::make($req->all(),[
    'id'=>'required',
    'name'=>'required|string',
    'email'=>'required|email|string'
]);
if($validate->fails()){
    return response()->json($validate->errors());
}
$user=User::find($req->id);
$user->name=$req->name;
if($user->email != $req->email){
$user->is_verified=0;
}
$user->email=$req->email;
$user->save();
return response()->json(['success'=>true,'msg'=>'user data updated','data'=>$user]);
}else{
    return response()->json(['success'=>false,'msg'=>'user isnot authenticated']);
}
    }

    public function sendVerificatioMail($email){
if(auth()->user()){

$user=User::where('email',$email)->get();
if(count($user)>0){
    $random=Str::random(40);
    $domain=URL::to('/');
    $url=$domain.'/account-verification/'.$random;//dynamic url

    $data['url']=$url;
    $data['email']=$email;
    $data['title']='email verification';
    $data['body']='please click here to below to verify your mail';
    Mail::send('verify_mail',['data'=>$data],function($message) use($data){
        $message->to($data['email'])->subject($data['title']);
    });
  $user=User::find($user[0]['id']);
  $user->remember_token=$random;
  $user->save();
  return response()->json(['success'=>true,'msg'=>'mail sent successfully']);


}else{
    return response()->json(['success'=>false,'msg'=>'user isnot found']);
}
}else{
    return response()->json(['success'=>false,'msg'=>'user isnot authenticated']);
}
    }
    public function verificationMail($token){
       $user= User::where('remember_token',$token)->get();
       if(count($user)>0){
        $datetime=Carbon::now()->format('Y-m-d H:i:s');
$user=User::find($user[0]['id']);
$user->remember_token='';
$user->email_verified_at=$datetime;
$user->is_verified=1;
$user->save();
return "<h1 style='color:green;'>email verified successfully</h1>";
       }else{
        return view('404');
       }
    }

    //refresh token api method_exists

    public function refreshToken(){
        if(auth()->user()){

            return $this->respondWithToken(auth()->refresh());
        }else{
return response()->json(['success'=>false,'msg'=>'user is not authenticated']);
        }
    }
    
    //forget password api method
    public function forgetPassword(Request $req){
try{
$user=User::where('email',$req->email)->get();
if(count($user)>0){
$token=Str::random(40);
$domain=URL::to('/');
$url=$domain.'/reset-password?token='.$token;
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

        $user=User::where('email',$resetdata[0]['email'])->get();
      
      // print_r($user[0]['email']);
        return view('resetPassword',compact('user'));
       }else{
        return view('404');

       }
    }
    //password reset functionality
    public function resetPassword(Request $req){
$req->validate([
    'password'=>'required|string|min:6|confirmed'
]);
$user=User::find($req->id);
$user->password=Hash::make($req->password);
$user->save();
PasswordReset::where('email',$user->email)->delete();
return "<h1>Your password has been updated successfully!</h1>";
    }
}
