<?php

namespace App\Http\Controllers;
use App\Ad;
use Illuminate\Http\Request;

class AddCampaign extends Controller
{
	public function createAdd(Request $request) {
		$campaign = new Ad;
    	$campaign->partner_id = $request->partner_id;
        $campaign->ad_content = $request->ad_content;
        $campaign->duration = $request->duration;
        $campaign->created_date =  date("Y-m-d h:i:s");
        $ads = Ad::where('partner_id', $request->partner_id) 
        ->get();
        // Try to manage the multiple adds for the single partner id, but only one ad is active for the current time stamp.
        $isAdExpired = false;
            if (sizeof($ads) == 0) {
                $campaign->save();
                return response()->json([
                    "message" => "Ad created"
                ], 201);
            } else {
                foreach ($ads as $add) {
                        $adate = $add->created_date;
                        $dateinsec =strtotime($adate);
                        $newdate = $dateinsec+$add->duration;
                        $latestDate = date('Y-m-d h:i:s',$newdate);
                    if ( $latestDate < date("Y-m-d h:i:s")) {
                        $isAdExpired = true;
                    } else {
                        $isAdExpired = false;
                    }
                }
            if ($isAdExpired) {
                $campaign->save();
                return response()->json([
                    "message" => "Ad created"
                ], 201);
            } else {
                return response()->json([
                    'error' => 'Add already exist for this time period'
                ], 404);    
            }
           }
       
	}

    public function getAllAdds() {

        $ads = Ad::get()->toJson(JSON_PRETTY_PRINT);
        return response($ads, 200);
    }

  public function getAd($partnerId) {
        //$ad = Ad::where('partner_id', $partnerId)->first();

        $ads = Ad::where('partner_id', $partnerId) 
        ->get();

        $isAdExpired = false;
        $newAd = [];
            if (sizeof($ads) == 0) {
                return response()->json([
                    "message" => "No ad campaigns exist for the specified partner"
                ], 404);
            } else {
                foreach ($ads as $add) {
                        $adate = $add->created_date;
                        $dateinsec =strtotime($adate);
                        $newdate = $dateinsec+$add->duration;
                        $latestDate = date('Y-m-d h:i:s',$newdate);
                    if ( $latestDate < date("Y-m-d h:i:s")) {
                        $isAdExpired = true;
                    } else {
                        array_push($newAd, $add);
                        $isAdExpired = false;
                    }
                }
            if ($isAdExpired) {
                 return response()->json([
                    'error' => 'No active ad campaigns exist for the specified partner'
                ], 404);  
            } else {
                return response()->json([
                    $newAd
                ], 201);
                 
            }
           }
  }
}
