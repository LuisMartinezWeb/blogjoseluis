<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User; 

class UserController extends Controller
{
    public function pruebas(Request $request){
        return "Accion de pruebas user controller";

    }

    public function register(Request $request){

        //recoger los datos del usuario por post

        $json = $request->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true);


        if(!empty($params) && !empty($params_array)){

            //limpiar datos

            $params_array = array_map('trim', $params_array);

            // validar los datos

            $validate = \Validator::make($params_array,[
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users', //comprobar si el usuario existe ya(duplicado unique)
                'password'     => 'required',
                

            ]);

            if($validate->fails()){

                $data =  array(
                    'status' => 'error',
                    'code' => 404,
                    'mensaje' => 'el usuario no se ha creado',
                    'errors'  => $validate->errors()
                );
            }else{
                //validacion pasada correctamente

                //cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                // crear el usuario

                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //guaradar el usuario
                $user->save();

                $data =  array(
                    'status' => 'success',
                    'code' => 202,
                    'mensaje' => 'el usuario se ha creado correctamente',
                    'user' => $user
                );

                

               
            }
        }else{

            $data =  array(
                'status' => 'error',
                'code' => 404,
                'mensaje' => 'Los datos enviados no son correctos',
            );


        }
  

        return response()->json($data,$data['code']);
    }

    public function login(Request $request){
       $jwtAuth = new \JwtAuth();

       //recibir los datos por post
        $json = $request->input('json', null);
        $params = json_decode($json); //objeto
        $params_array = json_decode($json, true);
       //validar los datos
       $validate = \Validator::make($params_array,[
        'email'     => 'required|email', 
        'password'     => 'required',
        

       ]);

        if($validate->fails()){

            $signup =  array(
                'status' => 'error',
                'code' => 404,
                'mensaje' => 'el usuario no se ha podido identificar',
                'errors'  => $validate->errors()
            );
        }else{
            //cifrar la contraseña

            $pwd = hash('sha256', $params->password);

             //devolver token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);

            if(!empty($params->gettoken)){
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }
       

        //var_dump($pwd); die();

       return response()->json($signup, 200);
    }


    public function update(Request $request){


        //comprobar si el usuario esta identificado

        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);    

         //recoger los datos por post
         $json = $request->input('json', null);     
         $params_array = json_decode($json,true);   


        if($checkToken && !empty($params_array)){
            //actualizar el usuario
  
           

            //sacar usuario identificado
            $user = $jwtAuth->checkToken($token,true);
            
            //validar los datos
            $validate = \Validator::make($params_array,[
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users,'.$user->sub   //comprobar si el usuario existe ya(duplicado unique)
            ]);
            //quitar los campos que no se quieren actualizar
            unset($params_array['id']); 
            unset($params_array['role']); 
            unset($params_array['password']);
            unset($params_array['create_at']);  
            unset($params_array['remember_token']);    

            //actualizar usuario en la base de datos
            $user_update = User::where('id', $user->sub)->update($params_array);

            //revolver array con resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array,
               );
            
        }else{
           $data = array(
            'code' => 404,
            'status' => 'error',
            'mensaje' => 'el usuario no esta identificado',
           );
        }

        return response()->json($data,$data['code']);
    }

    public function upload(Request $request){
        //recoger los datos de la peticion
        $image = $request->file('file0');

        //validar la imagen
        $validate = \Validator::make($request->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        
        if(!$image || $validate->fails()){
            //devolver el resultado
            $data = array(
                'code' => 404,
                'status' => 'error',
                'mensaje' => 'Error al subir imagen',
            );
           
        }else{
            //guardar la imagen
            $image_name = time().$image->getClientOriginalName();
            \Storage::disk('users')->put($image_name,\File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name,
            );
        }
        return response()->json($data,$data['code']);
    }

    public function getImage($filename){

        $isset = \Storage::disk('users')->exists($filename);

        if($isset){
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        }else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'mensaje' => 'La imagen no existe',
            );
        }
        return response()->json($data,$data['code']);
        
    }

    public function detail($id){
        $user = User::find($id);

        if(is_object($user)){
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user,
            );
        }else{
            $data = array(
                'code' => 404,
                'status' => 'error',
                'mensaje' => 'El usuario no existe',
            );
        }
        return response()->json($data,$data['code']);
    }
}
