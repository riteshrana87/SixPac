<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\OnDemandService;
use App\Models\GetFit;

class DemandServices
{

   public function __construct()
    {
    }

    /**
     * storeService is used to store service data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function storeService(Request $request): ?array
    {
        try {
            $input = $request->all();
            $demandService = OnDemandService::create($input);
            return ['status' => true,'message'=>'','data'=>$demandService];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }   
    }

    /**
     * updateService is used to update workout category data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function updateService(Request $request): ?array
    {
        try {
            $input = $request->all();
            $demandService = OnDemandService::findOrFail($request->id);
            $demandService->fill($input);
            $status = $demandService->save() ? true : false;
            return ['status' => $status,'message'=> $status ? 'Services has been updated!.' : 'Unable to update services!.','data'=>$demandService];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }   
    }

    /**
     * getFitList is used to get fit list
     *
     * @return array getfit list array
     * @author Spec Developer
     */
    public function getFitList(): ?array
    {
        return GetFit::where('status', 1)->pluck('name', 'id')->toArray();
    }
}
