<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DomainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

     return response()->json(['domains' =>  Domain::all()], 200);

    }

     public function create(Request $request)
     {
        $domain = new Domain;

       $domain->domain_name = $request->domain_name;
       $domain->tld = $request->tld;
       
       $domain->save();

       return response()->json(['domain' =>  $domain], 200);
     }

     public function show($id)
     {
        try {
            $domain = Domain::findOrFail($id);

            return response()->json(['domain' => $domain], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'domain not found!'], 404);
        }

        return response()->json($domain);
     }

     public function update(Request $request, $id)
     { 
        try{
            $domain= Domain::findOrFail($id);
        
            $domain->domain_name = $request->input('domain_name');
            $domain->tld = $request->input('tld');

            $domain->save();

            return response()->json(['domain' => $domain], 200);
        }catch(\Exception $e){

            return response()->json(['message' => 'domain not found!'], 404);
        }
     }

     public function destroy($id)
     {
        try{
            $domain = Domain::findOrFail($id);
            $domain->delete();

            return response()->json(['message' => 'domain removed successfully'], 200);
        }catch(\Exception $e){

            return response()->json(['message' => 'domain not found!'], 404);
        }
     }

     public function export_all()
     {
        try{
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename=export.csv');
            header("Content-Transfer-Encoding: UTF-8");
            

            $domains = Domain::all();

            $jsonEncoded = json_encode($domains);

            $jsonDecoded = json_decode($domains, true);

            $file = fopen('php://output', 'a');

            $header = false;

            foreach ($jsonDecoded as $row) {
                if (empty($header))
                {
                    $header = array_keys($row);
                    fputcsv($file, $header);
                    $header = array_flip($header);
                }
                fputcsv($file, array_merge($header, $row));
            }

            fclose($file);
            return response()->json(['message' => 'CSV downloaded successfully'], 200);
        }catch(\Exception $e){

            return response()->json(['message' => 'There was a problem downloading your export!'], 500);
        }
     }

     public function bulk_import(Request $request){

        if(!$request->hasFile('csv')) {
            return response()->json(['message' => 'upload file not found'], 400);
        }

        $file = $request->file('csv');

        if(!$file->isValid()) {
            return response()->json(['message' => 'invalid file upload'], 400);
        }

        try{
            $path = 'import.csv';


            $open = fopen($file, 'r');

            while (($data = fgetcsv($open)) !== FALSE) {
                $domain = new Domain;

                $domain->domain_name = $data[0];
                $domain->tld = $data[1];
                $domain->created_at = $data[2];
                $domain->updated_at = $data[3];
        
                $domain->save();
            }

            
            $file->move($path, $file->getClientOriginalName());
            
            return response()->json(['message' => 'CSV imported successfully: ' . compact('path')], 200);
        }catch(\Exception $e){

            return response()->json(['message' => 'There was a problem importing your data!'], 500);
        }
     }


}