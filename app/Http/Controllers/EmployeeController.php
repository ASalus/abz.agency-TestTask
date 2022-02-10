<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeDatatable;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\Employee;
use App\Models\Hierarchies;
use App\Models\Position;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

use function PHPUnit\Framework\isNull;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(EmployeeDatatable $dataTable)
    {
        return $dataTable->render('employees'); //return
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $req)
    {

        $validator = Validator::make($req->all(), $req->rules());

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'message' => 'There are incorrect values in the form!',
                'errors' => $validator->errors()->all()
            ), 422);
        }

        $employee = new Employee();

        $id = DB::select("SHOW TABLE STATUS LIKE 'employees'");
        $next_id = $id[0]->Auto_increment;

        if ($req->hasFile('image')) {

            $image = $req->file('image');
            $input['imagename'] = $next_id . '.jpg';
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(300, 300)->save(public_path('images/'.$input['imagename']),80);
            $employee->image = $input['imagename'];
        }

        $employee->name = $req->input('name');
        $employee->email = $req->input('email');
        $employee->phone_number = $req->input('phone');
        $employee->position_id = Position::where('position_name', $req->input('position_id'))->first()->id;
        $employee->salary = $req->input('salary');

        //________________________Hierarchy__Part__________________

        if (!empty($req->input('head'))) {
            $nameToId = Employee::where('name', $req->input('head'))->first()->id;
            $isHead = Hierarchies::where('head_id', '=', $nameToId)->first();
            $isSubordinate = Hierarchies::where('subordinate_id', '=', $nameToId)->first();

            if ($isHead === null)      //Check if potential employee can be supervisor
            {
                $hierarchy = new Hierarchies();

                if ($isSubordinate === null) //Check if he has supervisor
                {
                    $hierarchy_counter = Hierarchies::orderBy('hierarchy_id', 'desc')->first()->hierarchy_id;
                    if ($hierarchy_counter === null) {
                        $hierarchy_counter = 0;
                    }
                    $hierarchyId = $hierarchy_counter + 1;    //creating new hierarchy
                    $hierarchy->hierarchy_id = $hierarchyId;
                } else {
                    $headsHierarchyId = Hierarchies::where('subordinate_id', '=', $nameToId)->first()->hierarchy_id;
                    $counter = Hierarchies::where('hierarchy_id', '=', $headsHierarchyId)->get();
                    if (count($counter) >= 5) {
                        return response()->json(array(
                            'success' => false,
                            'errors' => ['head' => 'Hierarchy reached level 5']
                        ), 422);
                    }
                    $hierarchyId = $headsHierarchyId;
                    $hierarchy->hierarchy_id = $hierarchyId;
                }
                $hierarchy->head_id = $nameToId;
                $hierarchy->subordinate_id = $next_id;
                $hierarchy->counter += 1;
            } else {
                return response()->json(array(
                    'success' => false,
                    'errors' => ['head' => 'Employee already has a subordinate']
                ), 422);
            }
            $hierarchy->save();
        }
        //________________________________________________________________

        $date = DateTime::createFromFormat('d.m.y', $req->input('date'));
        $employee->employment_date = $date;
        $employee->admin_updated_id = auth()->user()->id;
        $employee->admin_created_id = auth()->user()->id;

        $employee->save();
        return response()->json([
            'status' => 400,
            'success' => 'Data is successfully added'
        ]);
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
            $data = Employee::findOrFail($id);

            if ($data->headRelation === null) {
                return response()->json([
                    'result' => $data,
                    'position_name' => $data->position->position_name,
                    'head_name' => ''
                ]);
            };
            $headId = $data->headRelation->head_id;
            $head_name = Employee::where('id', $headId)->first()->name;
            return response()->json([
                'result' => $data,
                'position_name' => $data->position->position_name,
                'head_name' => $head_name
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
    public function update(EmployeeUpdateRequest $request, $id)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), $request->rules());

            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorrect values in the form!',
                    'errors' => $validator->errors()->all()
                ), 422);
            }

            $employee = Employee::find($id);
            if ($request->hasFile('image')) {
                $image_path = public_path('images/' . $employee->image);
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                $image = $request->file('image');
                $input['imagename'] = $id . '.jpg';
                $image_resize = Image::make($image->getRealPath());
                $image_resize->resize(300, 300)->save(public_path('images/'.$input['imagename']), 80);
                $employee->image = $input['imagename'];
            }

            $employee->name = $request->input('name');
            $employee->email = $request->input('email');
            $employee->phone_number = $request->input('phone');
            $employee->position_id = Position::where('position_name', $request->input('position_id'))->first()->id;
            $employee->salary = $request->input('salary');

            //dd($employee->headRelation); exit();
            //________________________Hierarchy__Part__________________
            if (!empty($request->input('head'))) {

                $nameToId = Employee::where('name', $request->input('head'))->first()->id;

                $isHead = Hierarchies::where('head_id', '=', $nameToId)->first();
                $isSubordinate = Hierarchies::where('subordinate_id', '=', $nameToId)->first();
                //Check if potential employee can be supervisor
                if (is_null($isHead) && ($employee->id !== $nameToId)) {
                    // Check if head is employees subordinate

                    if (!is_null($employee->subordinateRelation)){
                        if ($employee->subordinateRelation->subordinate_id == $nameToId)
                        {
                            return response()->json(array(
                                'success' => false,
                                'errors' => ['head' => 'Chosen employee is a supervisor of '.$employee->name]
                            ), 422);
                        }
                    }
                    $hierarchy = Hierarchies::where('subordinate_id',$employee->id)->first();
                    if(is_null($hierarchy)){
                        $hierarchy = new Hierarchies();
                    }
                    if (is_null($isSubordinate)&&(is_null($employee->subordinateRelation)))    //Check if potential head has supervisor
                    {
                        $hierarchy_counter = Hierarchies::orderBy('hierarchy_id', 'desc')->first()->hierarchy_id;
                        if ($hierarchy_counter === null) {
                            $hierarchy_counter = 0;
                        }
                        $hierarchyId = $hierarchy_counter + 1;  //creating new hierarchy
                        $hierarchy->hierarchy_id = $hierarchyId;
                    } else if(!is_null($employee->subordinateRelation)&&(Hierarchies::where('hierarchy_id', '=', $employee->subordinateRelation->hierarchy_id == 5)->get()))
                    {
                        return response()->json(array(
                            'success' => false,
                            'errors' => ['head' => 'Hierarchy reached level 5']
                        ), 422);
                    }
                    else {
                        $headsHierarchyId = Hierarchies::where('subordinate_id', '=', $nameToId)->first()->hierarchy_id;
                        $counter = Hierarchies::where('hierarchy_id', '=', $headsHierarchyId)->get();
                        // checking that hierarchy is not reached level 6
                        if (count($counter) > 6) {
                            return response()->json(array(
                                'success' => false,
                                'errors' => ['head' => 'Hierarchy reached level 5']
                            ), 422);
                        }
                        $hierarchyId = $headsHierarchyId;
                        $hierarchy->hierarchy_id = $hierarchyId;
                    }
                    $hierarchy->head_id = $nameToId;
                    $hierarchy->subordinate_id = $id;
                    $hierarchy->counter += 1;
                    $hierarchy->save();
                }
                else if($employee->id == $nameToId)
                {
                    return response()->json(array(
                        'success' => false,
                        'errors' => ['head' => 'Cant be a supervisor of himself']
                    ), 422);
                }
                else if((!is_null($employee->headRelation)) && ($employee->headRelation->head_id==$nameToId))
                {}
                else
                {
                    return response()->json(array(
                        'success' => false,
                        'errors' => ['head' => 'Employee already has a subordinate']
                    ), 422);
                }
            }
            //___________________________________________________
            $date = DateTime::createFromFormat('d.m.y', $request->input('date'));
            $employee->employment_date = $date;
            $employee->admin_updated_id = auth()->user()->id;


            $employee->save();

            return response()->json([
                'status' => 400,
                'success' => 'Data is successfully updated'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image_path = "images/" . Employee::find($id)->image;
        // Deleting image of employee from server
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        $employee = Employee::find($id);

        // Checking if employee had a subordinate, and create a response
        if ($subordinateId = $employee->subordinateRelation !== null) {
            $subordinateId = $employee->subordinateRelation->subordinate_id;
            $response_array = [
                'success' => true,
                'message' => 'Data about the employee has been deleted',
                'headChange' => $subordinateId,
                'needToChangeHead' => true
            ];
        } else {
            $response_array = [
                'success' => true,
                'message' => 'Data about the employee has been deleted',
                'needToChangeHead' => false
            ];
        }
        $employee->delete();
        return response()->json($response_array);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('term');

        $result = Employee::where('name', 'LIKE', '%' . $search . '%')->get();
        return response()->json($result);
    }
}
