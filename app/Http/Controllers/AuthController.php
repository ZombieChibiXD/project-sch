<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
class AuthController extends Controller
{
    protected $username = 'username';
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|confirmed',
            'user_image' => 'image|nullable|max:2999'
        ]);
        if($request->hasFile('user_image')){
            // Get filename with the extension
            $filenameWithExt = $request->file('user_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('user_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore= time().'_'.md5($filename.'_'.time()).'.'.$extension;
            // Upload Image
            $path = $request->file('user_image')->storeAs('public/img/user_images', $fileNameToStore);
        } else {
            $fileNameToStore = 'no_image.jpg';
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'user_image' => $fileNameToStore
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!',
            'status'=>'201'
        ], 201);
    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {

        $request->validate([
            'credential' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        
        $login = request()->input('credential'); 
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username'; 
        request()->merge([$fieldType => $login]);

        $credentials = request([$fieldType, 'password']);
        
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized',
                'status' => '401'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'status'=>'200'
        ]);
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out',
            'status'=>'200'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $respond = $request->user();
        $respond->status = '200';
        return response()->json($respond);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|string|email|unique:users',
            'password' => 'nullable|string|min:6|confirmed',
            'user_image' => 'image|nullable|max:2999'
        ]);
        $oldUserData = $request->user();
        $newUserData = User::findOrFail($oldUserData->id);
        $updateImage = false;
        if($request->hasFile('user_image')){
            $updateImage = true;
            // Get filename with the extension
            $filenameWithExt = $request->file('user_image')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('user_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore= time().'_'.md5($filename.'_'.time()).'.'.$extension;
            // Upload Image
            $path = $request->file('user_image')->storeAs('public/img/user_images', $fileNameToStore);
        }

        $newUserData->name      = $request->name ? $request->name : $oldUserData->name;
        $newUserData->email     = $request->email ? $request->email : $oldUserData->email;
        $newUserData->password  = $request->password ? bcrypt($request->password) : $oldUserData->password;
        $newUserData->user_image= $updateImage ? $fileNameToStore : $oldUserData->user_image;
        
        // return response()->json($newUserData, 200);

        if($newUserData->save()){
            return response()->json([
                'message' => 'Successfully updated user!',
                'status'=>'200'
            ], 200);
        }
        else{
            return response()->json([
                'message' => 'Failed updating user!',
                'status'=>'409'
            ], 409);
        }
    }
}