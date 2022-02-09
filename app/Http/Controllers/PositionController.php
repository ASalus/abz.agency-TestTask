<?php

namespace App\Http\Controllers;

use App\DataTables\PositionDataTable;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PositionDataTable $dataTable)
    {
        return $dataTable->render('positions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'positionName' => 'required|unique:App\Models\Position,position_name|max:255',
        ],
        [
            'positionName.required' => 'Position name required',
            'positionName.unique' => 'Position already exist'
        ]);
        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'message' => 'There are incorrect values in the form!',
                'errors' => $validator->getMessageBag()->toArray()
            ], 422);
        }
        $positions = new Position();

        $positions->position_name = $request->input('positionName');
        $positions->admin_updated_id = auth()->user()->id;
        $positions->admin_created_id = auth()->user()->id;

        $positions->save();

        return response()->json(['success' => 'Position has been added']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (request()->ajax()) {
            $data = Position::findOrFail($id);
            return response()->json([
                'result' => $data
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'positionName' => 'required|unique:App\Models\Position,position_name|max:255',
        ],
        [
            'positionName.required' => 'Position name required',
            'positionName.unique' => 'Position already exist'
        ]);
        if($validator->fails())
        {
            return response()->json(array(
                'success' => false,
                'message' => 'There are incorrect values in the form!',
                'errors' => $validator->getMessageBag()->toArray()
            ), 422);
        }
        $positions = Position::find($id);

        $positions->position_name = $request->input('positionName');
        $positions->admin_updated_id = auth()->user()->id;

        $positions->save();

        return response()->json(['success' => 'Position has been updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Position::find($id)->delete();
        // Value is not URL but directory file path
        return response()->json(['success' => 'Position has been deleted']);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('term');
        $result = Position::where('position_name', 'LIKE', '%'. $search. '%')->get();
        return response()->json($result);
    }
}
