<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PersonaImport;
use App\Services\ValidarPersona;
use Carbon\Carbon;
use App\Models\Administracion\Persona;


class personaController extends Controller
{
    //
    public function readCsv(Request $request)
    {
        // $request->validate([
        //     'archivo' => 'required|file|mimes:xlsx,xls,csv',
        // ]);
        $filePath = $request->file('archivo');

        // Verifica si se cargó un archivo
        if (!$filePath) {
            return response()->json(['error' => 'No se ha cargado ningún archivo.'], 400);
        }

        $file = fopen($filePath->getPathname(), 'r'); // Usa getPathname() para obtener la ruta completa
        $header = fgetcsv($file);
        $users = [];

        while ($row = fgetcsv($file)) {
            $users[] = array_combine($header, $row);
        }

        fclose($file);

        return response()->json($users, 200);
    }
    public function registroConCSV(Request $request)
    {
        //code
        $filePath = $request->file('archivo');

        // Verifica si se cargó un archivo
        if (!$filePath) {
            return response()->json(['error' => 'No se ha cargado ningún archivo.'], 400);
        }
        $import = new PersonaImport();
        Excel::import($import, $filePath);
        $respuesta = $import->devolverArray();
        return response()->json($respuesta, 200);
    }
    public function processCsv(Request $request)
    {
        // ... (código para leer el archivo CSV)
        $filePath = $request->file('archivo');

        // Verifica si se cargó un archivo
        if (!$filePath) {
            return response()->json(['error' => 'No se ha cargado ningún archivo.'], 400);
        }
        $import = new PersonaImport();
        Excel::import($import, $filePath);



        // $import = new AsussPersonaImport;
        $data = Excel::toArray(null, $filePath);
        $data = $data[0];
        array_shift($data);
        $personas = [];
        foreach ($data as  $user) {
            $ci = $user[3];
            $complemento = $user[4];
            //! aqui se coloca el codigo para consultar con la asus
            //* obtener el token
            //? ejecutar la consulta
            // estado en 1 si encuentra , usar loa datos de la asuss
            // si no lo encutara separar los observados 
            $validacionAsuss = new ValidarPersona();
            $datosPersona = $validacionAsuss->buscarPersona($ci, $complemento);
            //dd($this->datosPersona);
            if (isset($datosPersona['datosPersonaEnFormatoJson'])) {
                $personaModelo = [
                    'ci' => $datosPersona['datosPersonaEnFormatoJson']['numeroDocumento'],
                    'complemento' => $datosPersona['datosPersonaEnFormatoJson']['complemento'],
                    'nombres' => $datosPersona['datosPersonaEnFormatoJson']['Nombres'],
                    'primerApellido' => $datosPersona['datosPersonaEnFormatoJson']['PrimerApellido'],
                    'segundoApellido' => $datosPersona['datosPersonaEnFormatoJson']['SegundoApellido'],
                    'fechaNacimiento' => Carbon::createFromFormat('d/m/Y', $datosPersona['datosPersonaEnFormatoJson']['FechaNacimiento'], 'America/La_Paz')->format('Y-m-d'),
                    'estado_asuss' => 1,
                    'estadoBusqueda' => 2,
                ];
                $this->registrarPersona($personaModelo);
                array_push($personas, $personaModelo);
            }
        }
        // Excel::import($import, $personas);
        return response()->json($personas, 200);
    }
    public function registrarPersona($persona)
    {
        $personaAsuss = Persona::create([
            'ci' => $persona['ci'],
            'complemento' => $persona['complemento'],
            'nombres' => $persona['nombres'],
            'primerApellido' => $persona['primerApellido'],
            'segundoApellido' => $persona['segundoApellido'],
            'fechaNacimiento' => $persona['fechaNacimiento'],
            'estado_asuss' => $persona['estado_asuss'],
            // 'estadoBusqueda' => $persona['estadoBusqueda'],
        ]);
        if (!$personaAsuss) {
            $data = [
                'status' => 500,
                'message' => 'Error al crear la persona',
            ];
            return response()->json($data, 500);
        }
        return response()->json([
            'status' => 200,
            'message' => 'se creo la persona',
        ], 500);
    }
}
