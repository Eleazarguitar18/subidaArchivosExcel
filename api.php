<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\personaAsusController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\NotificacionUsuarioController;
use App\Http\Controllers\personaController;
use App\Services\ConexionAsuss;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/testUser', [ConexionAsuss::class, 'login2']);
// * ruta para probar el envio de correos electronicos
Route::post('email', [EmailController::class, 'envioEmail']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// [personaAsusController::class,'listarPersonaAsus']
Route::post('excel', [ExcelController::class, 'archivo']);
Route::post('notificacion', [NotificacionUsuarioController::class, 'registrarNotificacion']);
Route::get('notificacion', [NotificacionUsuarioController::class, 'listarNotificacion']);
Route::get('notificacion/noleidos', [NotificacionUsuarioController::class, 'noLeidos']);
// * lectura de archivos csv para validar con el asus
Route::post('leerasus', [personaController::class, 'registroConCSV']);
// });




Route::get('/personaASUS', [personaAsusController::class, 'listarPersonaAsus']);

Route::get('/personaASUS/{id}', [personaAsusController::class, 'obtenerPersonaAsus']);
Route::post('/personaASUS', [personaAsusController::class, 'crearPersonaAsus']);
Route::put('/personaASUS/{id}', [personaAsusController::class, 'actualizarPersonaAsus']);
Route::delete('/personaASUS', function (Request $request) {
    return "este metodo es delete";
});
