<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;


class CategoryController extends Controller
{
    public function __construct(){
        $this->middleware('api.auth',['except' => ['index','show']]);
    }

    public function index(){
        $categories = Category::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'categories' => $categories,
        ]);
    }

    public function show($id){
        $category = Category::find($id);

        if(is_object($category)){
           $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category,
           ];
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'mensaje' => 'la categoria no existe',
           ];
        }

        return response()->json($data,$data['code']);
    }

    public function store(Request $request){
        //recoger los datos por post

        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if(!empty($params_array)){
        //validar los datos
        $validate = \Validator::make($params_array,[
            'name' => 'required',
        ]);
        //guardar la categoria
        if($validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'mensaje' => 'No se a guardado la categoria',
           ];
        }else{
            $category = new Category();
            $category->name = $params_array['name'];
            $category->save();

            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category,
           ];
        }
    }else{
        $data = [
            'code' => 400,
            'status' => 'error',
            'mensaje' => 'No has enviado ninguna categoria',
       ];
    }

        //devolver el resultado
        return response()->json($data,$data['code']);

    }


    public function update($id, Request $request){
        //recoger los datos
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        if(!empty($params_array)){
        //validar los datos

        $Validate = \Validator::make($params_array,[
            'name' => 'required',
        ]);

        //quitar lo que no se va actualizar

        unset($params_array['id']);
        unset($params_array['create_at']);
        

        //actualizar el registro

        $category = Category::where('id',$id)->update($params_array);

        $data = [
            'code' => 200,
            'status' => 'successs',
            'category' => $params_array,
       ];

        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'mensaje' => 'No has enviado ninguna categoria',
           ];
        }

        

        //devolver los datos
        return response()->json($data,$data['code']);
    }
}