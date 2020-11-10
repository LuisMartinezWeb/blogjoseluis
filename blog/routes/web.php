<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//cargando clases

use App\Http\Middleware\ApiAuthMiddleware;
//rutas de prubas

Route::get('/', function () {
    return view('welcome');
});



Route::get('/pruebas/{nombre?}', function ($nombre = null) {

    $texto = '<h2>Texto desde una ruta<h2>';
    $texto .= 'nombre:' .$nombre;
    return view('pruebas', array(
        'texto' => $texto
    ));
});

Route::get('/test-orm', 'PruebasController@testOrm');
Route::get('/animales', 'PruebasController@index');

//rutas de api


    //metodos http comunes

    // GET = conseguir datos o recursos
    // POST: guardar datos o recursos o hacer logica desde un formulario 
    // PUT: actualizar recursos o datos 
    // DELETE: Eliminar datos o recursos



 //rutas de prueba
//Route::get('/usuario/pruebas','UserController@pruebas');
//Route::get('/categoria/pruebas','CategoryController@pruebas');
//Route::get('/entrada/pruebas','PostController@pruebas');


//rutas del controlador de usuarios

Route::post('/api/register','UserController@register');
Route::post('/api/login','UserController@login');

Route::put('/api/user/update','UserController@update');
Route::post('/api/user/upload','UserController@upload')->middleware( ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}','UserController@getimage');
Route::get('/api/user/detail/{id}','UserController@detail');

//rutas del controlador de categorias


Route::resource('/api/category','CategoryController');

//rutas del controlador de entrada
Route::resource('/api/post','PostController');
Route::post('/api/post/upload','PostController@upload');
Route::get('/api/post/image/{filename}','PostController@getImage');
Route::get('/api/post/category/{id}','PostController@getPostsByCategory');
Route::get('/api/post/user/{id}','PostController@getPostsByUser');

