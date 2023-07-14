<?php

namespace App\Http\Controllers\api;

use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use Mail;//for default mail service in laravel
use Illuminate\Support\Str;//for generating random string
use Illuminate\Support\Facades\URL;//for generating dynamic url
use Illuminate\Support\Carbon;//for geting current datetime
use App\models\PasswordReset;
class StudentController extends Controller
{
public function register(Request $req){

    $validate=Validator::make($req->all(),[
        'name'=>'required|string|min:2|max:200',
    'email'=>'required|string|email|max:100|unique:students',
    'password'=>[
        'required',
        'confirmed',
        Password::min(6)
    ->letters()
    ->mixedCase()
    ->numbers()
    ->symbols()
   
    ],
    'branch'=>'required|string|min:2|max:3',
    'year'=>'required|max:1|min:1',
    'contact'=>'required',
    'rollno'=>'required|min:10',
    
    ]);
    if($validate->fails()){
        return response()->json($validate->errors());
    }
    
    $user=Student::create([
        'name'=>$req->name,
        'email'=>$req->email,
        'password'=>Hash::make($req->password),
        'branch'=>$req->branch,
        'contact'=>$req->contact,
        'rollno'=>$req->rollno,
        'year'=>$req->year,
    ]);
    
    
    return response()->json([
        'msg'=>'User registered successfully, Please login with your credential!',
        'user'=>$user,
    ]);
}
public function login(Request $req){
 $validate=Validator::make($req->all(),[
    'email'=>'required|email|string',
    'password'=>'required|string|min:6',
 ]);

 
 if($validate->fails())
 return response()->json($validate->errors());
 try{
    if(!$token=Auth::guard('student')->attempt($validate->validated())){
        return response()->json(['success'=>false ,'msg'=>'invalid credential']);
     }
 }catch(Exception $e){
return response()->json(['success'=>false,'msg'=>'token not generated']);
 }
 return $this->respondWithToken($token);

}

protected function respondWithToken($token){
    $user = Auth::guard('student')->id();

    return response()->json([
        'success'=>true,
        'msg'=>'user logged in successfully',
       'id'=>$user,
        'access_token'=>$token,
    'token_type'=>'Bearer',
    'expires_in'=>auth()->factory()->getTTL()*60,]);
} 


public function logout(){

    try{  auth()->logout();
   
        return response()->json(['success'=>true,'msg'=>'user logged out!']);
    }catch(\Exception $e){
        return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
    }
  
}

//mail sent for forget password
public function forgetPassword(Request $req){
    try{
        $user=Student::where('email',$req->email)->get();
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
 
         $user=Student::where('email',$resetdata[0]['email'])->get();
       
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
        $user=Student::find($req->id);
        $user->password=Hash::make($req->password);
        $user->save();
        PasswordReset::where('email',$user->email)->delete();
        return "<h1>Your password has been updated successfully!</h1>";
            }

            ///user info
            public function profile(){
        
                try{
                return response()->json(['success'=>true,'data'=>auth()->guard('student')->user()]);
                } catch(\Exception $e)
                {
                    return response()->json(['success'=>false,'msg'=>$e->getMessage()]);
                
                }
                    }


}
