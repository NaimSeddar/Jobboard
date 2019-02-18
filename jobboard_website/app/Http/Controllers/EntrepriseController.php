<?php

namespace App\Http\Controllers;

use App\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntrepriseController extends Controller
{
    function createEntreprise(){
        if(Auth::check()){
            return view('entreprise/createEntreprise');
        }
        return redirect(route('login'));
    }

    function enregistrerEntreprise(Request $request){
        $userID = Auth::id();

        $this->validate($request,
            [
                "nom"=> ["required","string","max:255"],
                "siret"=> ["required","string","min:14","max:14"],
            ]);
        $input=$request->only(["nom","siret"]);


        $entreprise = DB::table("entreprise")->insertGetId([
            "nom" => $input["nom"],
            "siret" => $input["siret"],
        ]);

        DB::table('contact_entreprise')->where('idUser',$userID)->update(['idEntreprise' => $entreprise]);


        $this->validate($request,[
           "nbAdresse"
        ]);

        $compteur = $request["nbAdresse"]+=0;
        for($i = 0; $i < $compteur; $i++) {
            $this->validate($request,[
                "adresse_".$i."_rue" => ['required', "string", "max:255"],
                "adresse_".$i."_ville" => ['required', "string", "max:255"],
                "adresse_".$i."_codePostal" => ['required', "string", "max:255"],
            ]);

            $input=$request->only(["adresse_".$i."_rue","adresse_".$i."_ville","adresse_".$i."_codePostal"]);

            DB::table('adress_entreprise')->insert([
               "nomRue" => $input["adresse_".$i."_rue"],
               "ville" => $input["adresse_".$i."_ville"],
               "coordonnePostales" => $input["adresse_".$i."_codePostal"],
                "idEntreprise" => $entreprise,
            ]);
        }

        $this->validate($request,[
            "nbContact",
        ]);

        $compteurContact = $request["nbContact"]+=0;

        for($i = 0; $i < $compteurContact; $i++){
            $this->validate($request,[
                "contact_".$i."_civilite" => ['required', "string", "max:255"],
                "contact_".$i."_nom" => ['required', "string", "max:255"],
                "contact_".$i."_prenom" => ['required', "string", "max:255"],
                "contact_".$i."_mail" => ['required', "string", "max:255"],
                "contact_".$i."_phone" => ['required', "string", "max:10", "min:10"],
            ]);

            $input = $request->only(["contact_".$i."_civilite","contact_".$i."_nom",
                "contact_".$i."_prenom","contact_".$i."_mail","contact_".$i."_phone"]);

            DB::table('contact_entreprise')->insert([
                'nom' => $input["contact_".$i."_nom"],
                'prenom' => $input["contact_".$i."_prenom"],
                'mail' => $input["contact_".$i."_mail"],
                'telephone' => $input["contact_".$i."_phone"],
                'civilite' => $input["contact_".$i."_civilite"],
                'idEntreprise' => $entreprise,
                ]);
        }

        return redirect(route('accueil'));
    }

    function editEntreprise($id){
        if (Auth::check()){
            $idUser = Auth::id();
            $idEntreprise = DB::table('contact_entreprise')->where('idUser',$idUser)->value('idEntreprise');

            if($idEntreprise == $id){
                $entreprise = Entreprise::find($id);
                return view('entreprise/edit', ['entreprise'=>$entreprise]);
            }
            return redirect(route('accueil'));
        }
        return redirect(route('login'));
    }

    function storeChanges(Request $request){
        $idUser = Auth::id();
        $entreprise = DB::table('contact_entreprise')->where('idUser',$idUser)->value('idEntreprise');

        $this->validate($request,
            [
                "nom"=> ["required","string","max:255"],
                "siret"=> ["required","string","min:14","max:14"],
            ]);
        $input=$request->only(["nom","siret"]);


        DB::table("entreprise")->where('id',$entreprise)->update([
            "nom" => $input["nom"],
            "siret" => $input["siret"],
        ]);


        $this->validate($request,[
            "nbAdresse"
        ]);

        DB::table('adress_entreprise')->where('idEntreprise', $entreprise)->delete();

        $compteur = $request["nbAdresse"]+=0;
        for($i = 0; $i < $compteur; $i++) {
            $this->validate($request,[
                "adresse_".$i."_rue" => ['required', "string", "max:255"],
                "adresse_".$i."_ville" => ['required', "string", "max:255"],
                "adresse_".$i."_codePostal" => ['required', "string", "max:255"],
            ]);

            $input=$request->only(["adresse_".$i."_rue","adresse_".$i."_ville","adresse_".$i."_codePostal"]);

            DB::table('adress_entreprise')->insert([
                "nomRue" => $input["adresse_".$i."_rue"],
                "ville" => $input["adresse_".$i."_ville"],
                "coordonnePostales" => $input["adresse_".$i."_codePostal"],
                "idEntreprise" => $entreprise,
            ]);
        }

        DB::table('contact_entreprise')->where('idEntreprise',$entreprise)->where('idUser',null)->delete();

        $this->validate($request,[
            "nbContact",
        ]);

        $compteurContact = $request["nbContact"]+=0;


        for($i = 0; $i < $compteurContact; $i++){
            $this->validate($request,[
                "contact_".$i."_civilite" => ['required', "string", "max:255"],
                "contact_".$i."_nom" => ['required', "string", "max:255"],
                "contact_".$i."_prenom" => ['required', "string", "max:255"],
                "contact_".$i."_mail" => ['required', "string", "max:255"],
                "contact_".$i."_phone" => ['required', "string", "max:10", "min:10"],
                "isUser_".$i => ['required'],
            ]);

            $input = $request->only(["contact_".$i."_civilite","contact_".$i."_nom",
                "contact_".$i."_prenom","contact_".$i."_mail","contact_".$i."_phone","isUser_".$i]);


            if (substr($input["isUser_".$i],0,6) == "false_" ){

            }
            elseif ($input["isUser_".$i]=="false"){
                DB::table('contact_entreprise')->insert([
                    'nom' => $input["contact_".$i."_nom"],
                    'prenom' => $input["contact_".$i."_prenom"],
                    'mail' => $input["contact_".$i."_mail"],
                    'telephone' => $input["contact_".$i."_phone"],
                    'civilite' => $input["contact_".$i."_civilite"],
                    'idEntreprise' => $entreprise,
                ]);
            }

            else{
                DB::table("contact_entreprise")->where("id", $input["isUser_".$i])->update([
                    'idEntreprise' => NULL,
                ]);
            }

        }

        return redirect(route('accueil'));
    }

    function afficheUneEntreprise($id){
        $entreprise = Entreprise::find($id);
        return view('entreprise/uneEntreprise',['entreprise'=>$entreprise]);
    }

}
