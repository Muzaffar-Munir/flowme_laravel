<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use \App\Http\Resources\User as RegisteredUserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->middleware('auth:api')->only('details');
        $this->user = $user;
    }

    public function login()
    {
        $errors = collect();
        $error = false;
        $statusCode = 0;

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            return response()->json([
                'user' => new RegisteredUserResource($user),
                'token' => $user->createToken('user')->accessToken,
            ], Response::HTTP_OK);
        } else {
            $error = true;
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $errors->push(['invalid_credentials' => ['Invalid credentials']]);
        }
        if ($error) {
            return response()->json([
                'message' => 'Something went wrong.',
                'errors' => $errors
            ], $statusCode);
        }
    }

    public function register(Request $request)
    {
        $input = $request->all();
        $this->validate($request,[
            'first_name' => 'required|min:3|max:555',
            'last_name' => 'required|min:3|max:555',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'phone_number' => 'required|unique:users,phone_number'
        ]);
        $input['password'] = bcrypt($input['password']);
        $input['type'] = 'user';
        $user = User::create($input);
        $success['message'] = 'User registered successfully.';
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('user')->accessToken,
            'success' => $success
        ], Response::HTTP_OK);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['user' => new RegisteredUserResource($user)], Response::HTTP_OK);
    }

    public function passwordForget(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['errors' => 'User with this email does not exists'], 404);
            }

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Str::random(60),
                'created_at' => Carbon::now()
            ]);

            $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

//            $event_type = "ForgetPassword";
//            SendGridMail::send($event_type, $user, $tokenData->token);

            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['errors' => true], 404);
        }
    }

    public function passwordResetView($token)
    {
        $table = DB::table('password_resets')->where('token', $token)->first();
        if ($table) {
            $url = config('env-variables.UI_APP_URL') . '/account/reset-password?email=' . $table->email . '&token=' . $token;
            return Redirect::to($url);
        }
        $url = config('env-variables.UI_APP_URL') . '/account/reset-password?email=?&token=' . $token;
        return Redirect::to($url);
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password_reset_token' => 'required',
                'email' => 'required',
                'password' => 'required',
                'confirm_password' => 'required|same:password'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 401);
            }

            $table = DB::table('password_resets')->where('token', $request->password_reset_token)->first();
            if ($table) {
                $user = User::where('email', $request->email)->first();
                if ($user) {
                    $user->password = bcrypt($request['password']);
                    $user->update();
                    return response()->json(['user' => $user], 200);
                } else {
                    return response()->json(['errors' => 'User with this details does not exists'], 404);
                }
            }
            return response()->json(['errors' => 'Token does not exists'], 404);
        } catch (Exception $e) {
            return response()->json(['errors' => 'Something went wrong. Try again later.'], 404);
        }
    }
}
