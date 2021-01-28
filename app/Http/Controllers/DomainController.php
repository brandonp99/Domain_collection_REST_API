<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;


class DomainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
     
     $domains = Domain::all();

     return response()->json($domains);

    }

     public function create(Request $request)
     {
        $domain = new Domain;

       $domain->domain_name = $request->domain_name;
       $domain->tld = $request->tld;
       $domain->created_at = $request->created_at;
       $domain->updated_at = $request->updated_at;
       
       $domain->save();

       return response()->json($domain);
     }

     public function show($id)
     {
        $domain = Domain::find($id);

        return response()->json($domain);
     }

     public function update(Request $request, $id)
     { 
        $domain= Domain::find($id);
        
        $domain->domain_name = $request->input('domain_name');
        $domain->tld = $request->input('tld');
        $domain->created_at = $request->input('created_at');
        $domain->updated_at = $request->input('updated_at');
        $domain->save();
        return response()->json($domain);
     }

     public function destroy($id)
     {
        $domain = Domain::find($id);
        $domain->delete();

         return response()->json('domain removed successfully');
     }

     public function export_all()
     {
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
     }


}