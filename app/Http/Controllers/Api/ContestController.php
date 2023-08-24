<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Contest;
use Validator;
use Auth;
use Carbon\Carbon;

class ContestController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('page') && $request->page!='') {
            $page           = $request->page;
            $resultCount    = 10;
            $offset         = ($page - 1) * $resultCount;

            $query          = Contest::query()->orderBy('id', 'DESC');
            $query->skip($offset);
            $query->take($resultCount);
            $contests       = $query->get();
        }else{
            $contests       = Contest::query()->orderBy('id', 'DESC')->get();
        }
        if($contests->count() > 0){
            $result         = $contests->toArray();
            return $this->sendResponse($this->setData($result), 'Contest retrieved successfully.');
        }
        return $this->sendError('No record found.');
    }

    public function contest_questions(Request $request){
        $validator = Validator::make($request->all(), [
            'contest_id' => 'required|numeric',
        ]);
        if($validator->fails()){
            return $this->sendError('Please fill all mandatory fields.',$validator->messages()->toArray());
        }
        $contest = Contest::with('contest_questions.contest_answers')->where('status',1)->first();
        if($contest){
            $result         = $contest->toArray();
            return $this->sendResponse($this->setData($result), 'Contest Questions retrieved successfully.');
        }
        return $this->sendError('No Contest found.');
    }
}
