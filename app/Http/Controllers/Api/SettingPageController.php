<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SettingWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingPageController extends Controller
{
    public function termsPage()
    {
        try {
            $terms = SettingWeb::select(DB::raw("terms_" . app()->getLocale() . " AS terms"))->first();
            return  response()->json([
                'data' => $terms->terms,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }

        public function aboutPage()
    {
        try {
            $about_us = SettingWeb::select(DB::raw("about_us_" . app()->getLocale() . " AS about_us"))->first();
            return response()->json([
                'data' => $about_us->about_us,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


        public function privacyPage()
    {
        try {
            $privacy = SettingWeb::select(DB::raw("privacy_" . app()->getLocale() . " AS privacy"))->first();
            return response()->json([
                'data' => $privacy->privacy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


        public function returnPolicyPage()
    {
        try {
            $return_policy = SettingWeb::select(DB::raw("return_policy_" . app()->getLocale() . " AS return_policy"))->first();
            return response()->json([
                'data' => $return_policy->return_policy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.'.$th, 'status_code' => 500], 500);
        }
    }

        public function storePolicyPage()
    {
        try {
            $store_policy = SettingWeb::select(DB::raw("store_policy_" . app()->getLocale() . " AS store_policy"))->first();
            return response()->json([
                'data' => $store_policy->store_policy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


        public function sellerPolicyPage()
    {
        try {
            $seller_policy = SettingWeb::select(DB::raw("seller_policy_" . app()->getLocale() . " AS seller_policy"))->first();
            return response()->json([
                'data' => $seller_policy->seller_policy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


        public function primeryColor()
    {
        try {
            $colors = SettingWeb::select("color_primery",'color_second_primery')->first();
            return response()->json([
                'colors' => $colors,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


    public function sendOtp()
    {
     





	// Authorisation details.
	$username = "tth31770@gmail.com";
	$hash = "f2382cce5d557133c88eb75a986de569168d9e08c9c433aed113afcbad44694f";

	// Config variables. Consult http://api.txtlocal.com/docs for more info.
	$test = "0";

	// Data for text message. This is the text message data.
	$sender = "API Test"; // This is who the message appears to be from.
	$numbers = "+201288964270"; // A single number or a comma-seperated list of numbers
	$message = "This is a test message from the PHP API script.";
	// 612 chars or less
	// A single number or a comma-seperated list of numbers
	$message = urlencode($message);
	$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
	$ch = curl_init('https://api.txtlocal.com/send/?');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); // This is the result from the API
     echo $result;    
        echo 'done';   
	curl_close($ch);


        return response()->json(['message' => $result , 'status_code' => 200], 200);



    }






}