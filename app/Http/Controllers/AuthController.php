<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $pointHistoryController;
    public function __construct(PointHistoryController $pointHistoryController)
    {
        $this->pointHistoryController = $pointHistoryController;
        // $this->middleware('auth:api', ['except' => ['login', 'register', 'changePassword', 'updateUser', 'updatePoints', 'addPoint', 'deleteUser']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
            'platform' => 'required|string',
        ]);

        $credentials = $request->only('user_name', 'password', 'platform');

        $checkplatform = User::select('id', 'platform')
            ->where('user_name', $credentials['user_name'])
            ->first();
        // dd($checkplatform['platform']);

        if ($checkplatform['platform'] == 'ios' || $checkplatform['platform'] == 'mac') {
            // dd('iside');
                $token = JWTAuth::attempt(['user_name' => $credentials['user_name'], 'password' => $credentials['password']]);
            if (!$token) {
                return response()->json([
                    'status' => false,
                    'message' => 'token not found',
                ], 401);
            }
            $user = JWTAuth::user();
            $update = User::where('appId', $user->appId)->update([
                'login_status' => '1'
            ]);
            $data = Customer::where('reseller_id', $user->id)->get();
            return response()->json([
                'status' => true,
                'user' => $user,
                'customer' => $data,
                'policy' => 'https://www.nxtlevel.live/privacy-policy',
                'message' => 'Login Successfully',
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        } else {
            if ($credentials['platform'] == 'windows' || $credentials['platform'] == 'android') {
                $token = JWTAuth::attempt(['user_name' => $credentials['user_name'], 'password' => $credentials['password']]);
                // dd('if',$token);
                if (!$token) {
                    return response()->json([
                        'status' => false,
                        'message' => 'token not found',
                    ], 401);
                }
                $user = JWTAuth::user();
                $update = User::where('appId', $user->appId)->update([
                    'login_status' => '1'
                ]);
                $data = Customer::where('reseller_id', $user->id)->get();
                return response()->json([
                    'status' => true,
                    'user' => $user,
                    'customer' => $data,
                    'policy' => 'https://www.nxtlevel.live/privacy-policy',
                    'message' => 'Login Successfully',
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            } else {
                // dd('else');
                return response()->json([
                    'status' => true,
                    'message' => 'You are not allowed to login in this platform'

                ]);
            }
        }
    }

    public function updatePoints(string $rId, string $point)
    {

        $update = User::where('id', $rId)->update([
            'points' => $point,
        ]);
        if ($update) {
            $data = User::where('appId', $rId)->first();
            return true;
        }
    }
    
    public function changePassword(Request $request)
    {
        $id = $request->appId;
        $update = User::where('appId', $id)->update([
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
        ]);
        if ($update) {
            $data = User::where('appId', $id)->first();
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Password Updated Successfully'
            ]);
        }
    }

    public function updateUser(Request $request)
    {
        $id = $request->appId;
        $update = User::where('appId', $id)->update([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'mobile' => $request->mobile,
            'image_index' => $request->image_index,
        ]);
        if ($update) {
            $data = User::where('appId', $id)->first();
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'User Updated Successfully'
            ]);
        }
    }

    public function addPoint(Request $request)
    {
        $id = $request->appId;
        $rid = $request->rid;
        $point = $request->points;
        $resellerEndDate = $request->reseller_end_date;
        $userEndDate = $request->user_end_date;

        $user = User::where('appId', $id)->first();

        $resellerUser = User::where('id', $rid)->first();
        $resellerUser->end_date = $resellerEndDate;
        $resellerUser->save();


        $remark = "$point point added by $resellerUser->name to $user->name";
        $result = $this->pointHistoryController->addPoints($resellerUser->id, $user->id, $request->points, $remark);

        $user->point_history = $result->id;
        $user->end_date = $userEndDate;
        $user->save();
        return response()->json([
            'status' => true,
            'message' => 'Point added Successfully'
        ]);
    }

    public function reversePoint(Request $request)
    {
        $id = $request->appId;
        $rid = $request->rid;
        $point = $request->points;
        $resellerEndDate = $request->reseller_end_date;
        $userEndDate = $request->user_end_date;

        $user = User::where('appId', $id)->first();

        $resellerUser = User::where('id', $rid)->first();
        $resellerUser->end_date = $resellerEndDate;
        $resellerUser->save();

        $remark = "$point point reversed by $resellerUser->name from $user->name";
        $result = $this->pointHistoryController->addPoints($resellerUser->id, $user->id, $request->points, $remark);

        $user->point_history = "0";
        $user->end_date = $userEndDate;
        $user->save();
        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Pointed Reversed Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request failed'
            ]);
        }
    }

    public function register(Request $request)
    {
        $user = User::where('user_name', $request->user_name)->first();
        if ($user) {
            return response()->json([
                'status' => false,
                'message' => 'User already exists'
            ]);
        }
        $user = new User;
        $user->appId = 'app_' . uniqid();
        $user->name = $request->name;
        $user->user_name = $request->user_name;
        $user->password = Hash::make($request->password);
        $user->temp_pass = $request->password;
        $user->login_status = '0';
        $user->start_date = $request->start_date;
        $user->end_date = $request->end_date;
        $user->image_index = $request->image_index;
        $user->role_id = $request->role_id;
        $user->reseller_id = $request->reseller_id;
        $user->mobile = $request->mobile;
        //platform type:- ios/android/mac/windows
        $user->platform = $request->platform;
        $user->save();

        $resellerName =  User::where('id', $request->reseller_id)->first();
        $resellerName->end_date = $request->reseller_end_date;
        $resellerName->save();

        $remark = "$request->points point added by $resellerName->name to $request->username";
        $result = $this->pointHistoryController->addPoints($request->reseller_id, $user->id, $request->points, $remark);

        $UpdateDetails = User::where('id', '=',  $user->id)->first();
        $UpdateDetails->point_history = $result->id;
        $UpdateDetails->save();
        $token = Auth::login($user);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'user' => $user,
            //   'updatedpoin' => $request->points,
            'policy' => 'https://www.nxtlevel.live/privacy-policy',
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $user_appId = $request->user_appId;

        $status = User::where('appId', $user_appId)->get();
        if (count($status) > 0) {
            $update = User::where('appId', $user_appId)->update([
                'login_status' => '0'
            ]);
        }
        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    public function deleteUser($id)
    {
        $delete = User::where('id', $id)->delete();
        if ($delete) {
            return response()->json([
                'status' => true,
                'message' => 'User Deleted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request failed'
            ]);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => true,
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
// app_639deda825c7e