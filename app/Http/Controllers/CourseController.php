<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;


class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

 public function index(){
     return Course::paginate();
 }

 public function show($id){
     return Course::find($id);

 }

 public function create(Request $request){
    $course = new Course();
    $course->code =  $request->input('code');
    $course->name =  $request->input('name');
    $course->description = $request->input('description');
    $course->save();
    return json_encode(['msg'=>'added']);
 }

 public function destroy($id){
     Course::destroy($id);
     return json_encode(["msg"=>"removed"]);
 }

 public function edit(Request $request, $id){
    $code =  $request->input('code');
    $name =  $request->input('name');
    $description = $request->input('description');
    Course::where('id', $id)->update(
      ['code'=>$code,
       'name'=>$name,
       'description'=>$description]
    );
     return json_encode(["msg"=>"edited"]);
 }

}
