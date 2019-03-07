<?php

namespace App\Http\Controllers;

use App\AdressEntreprise;
use App\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntrepriseController extends Controller
{
    function canCreate() {
        foreach (Auth::user()->roles as $role){
            if ($role->typeRole == "ADMIN" || $role->typeRole == "CONTACT") return true;
        }
        return false;
    }

    function createEntreprise(){
        if(Auth::check() && $this->canCreate()){
            $contacts = DB::table('contact_entreprise')->where('idEntreprise',null)->get();
            return view('entreprise/createEntreprise', ['contacts' => $contacts]);
        }
        return redirect(route('login'));
    }

    function enregistrerEntreprise(Request $request){
        $userID = Auth::id();

        $contactID = DB::table('contact_entreprise')->where('idUser',$userID)->value('id');

        $this->validate($request,
            [
                "nom"=> ["required","string","max:255"],
                "siret"=> ["required","string","min:14","max:14"],
                "description" =>["required", "string", "min:15", 'max:1000'],
            ]);
        $input=$request->only(["nom","siret","description"]);


        $entreprise = DB::table("entreprise")->insertGetId([
            "nom" => $input["nom"],
            "siret" => $input["siret"],
            "description" => $input["description"],
            "createur" => $contactID,
            "actif" => 1
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
            "nbContactExist",
        ]);

        $compteurContact = $request["nbContactExist"]+=0;

        for($i = 0; $i < $compteurContact; $i++){
            $this->validate($request,[
                "contact_".$i =>['required'],
            ]);
            $input = $request->only(["contact_".$i]);

            DB::table('contact_entreprise')->where('id', $input["contact_".$i])->update([
                'idEntreprise' => $entreprise,
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
                "contact_".$i."_phone" => ['nullable', "string", "max:10", "min:10"],
                "contact_".$i."_role" => ['required', "string"],
            ]);

            $input = $request->only(["contact_".$i."_civilite","contact_".$i."_nom",
                "contact_".$i."_prenom","contact_".$i."_mail","contact_".$i."_phone","contact_".$i."_role"]);

            DB::table('contact_entreprise')->insert([
                'nom' => $input["contact_".$i."_nom"],
                'prenom' => $input["contact_".$i."_prenom"],
                'mail' => $input["contact_".$i."_mail"],
                'telephone' => $input["contact_".$i."_phone"],
                'civilite' => $input["contact_".$i."_civilite"],
                'role' => $input["contact_".$i."_role"],
                'idEntreprise' => $entreprise,
                ]);
        }

        return redirect(route('accueil'));
    }

    function isAdmin() {
        foreach (Auth::user()->roles as $role){
            if ($role->typeRole == "ADMIN") return true;
        }
        return false;
    }

    function editEntreprise($id){
        if (Auth::check()){
            $idUser = Auth::id();
            $idEntreprise = DB::table('contact_entreprise')->where('idUser',$idUser)->value('idEntreprise');

            if($idEntreprise == $id || $this->isAdmin()){
                $entreprise = Entreprise::find($id);
                $contacts = DB::table('contact_entreprise')->where('idEntreprise',null)->get();

                return view('entreprise/edit', ['entreprise'=>$entreprise, 'contacts'=>$contacts]);
            }
            return redirect(route('accueil'));
        }
        return redirect(route('login'));
    }

    function storeChanges(Request $request, $id){
        $idUser = Auth::id();
        $entreprise = Entreprise::find($id);

        $this->validate($request,
            [
                "nom"=> ["required","string","max:255"],
                "siret"=> ["required","string","min:14","max:14"],
                "description" =>["required", "string", "min:15", 'max:1000'],
                "createur" => ["required"],
                "actif" => ["required", 'integer'],
            ]);
        $input=$request->only(["nom","siret","description","createur","actif"]);


        DB::table("entreprise")->where('id',$entreprise->id)->update([
            "nom" => $input["nom"],
            "siret" => $input["siret"],
            "description" => $input["description"],
            "createur" => $input["createur"],
            "actif" => $input["actif"],
        ]);


        $this->validate($request,[
            "nbAdresse"
        ]);


        $adresses = AdressEntreprise::where('idEntreprise', $entreprise->id)->get();

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
                "idEntreprise" => $entreprise->id,
            ]);
        }

        foreach ($adresses as $adresse){
            $adresse->delete();
        }

        DB::table('contact_entreprise')->where('idEntreprise',$entreprise)->where('idUser',null)->delete();

        $this->validate($request,[
            "nbContactExist",
        ]);

        $compteurContactExist = $request["nbContactExist"]+=0;

        for($i = 0; $i < $compteurContactExist; $i++){
            $this->validate($request,[
                "contact_".$i =>['required'],
            ]);
            $input = $request->only(["contact_".$i]);

            DB::table('contact_entreprise')->where('id', $input["contact_".$i])->update([
                'idEntreprise' => $entreprise,
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
                "contact_".$i."_phone" => ['nullable', "string", "max:10", "min:10"],
                "contact_".$i."_role" => ['required', "string"],
                "isUser_".$i => ['required'],
            ]);

            $input = $request->only(["contact_".$i."_civilite","contact_".$i."_nom",
                "contact_".$i."_prenom","contact_".$i."_mail","contact_".$i."_phone","contact_".$i."_role","isUser_".$i]);


            if (substr($input["isUser_".$i],0,6) == "false_" ){

            }
            elseif ($input["isUser_".$i]=="false"){
                DB::table('contact_entreprise')->insert([
                    'nom' => $input["contact_".$i."_nom"],
                    'prenom' => $input["contact_".$i."_prenom"],
                    'mail' => $input["contact_".$i."_mail"],
                    'telephone' => $input["contact_".$i."_phone"],
                    'civilite' => $input["contact_".$i."_civilite"],
                    'role' => $input["contact_".$i."_role"],
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
        if (Auth::check()){
            $entreprise = Entreprise::find($id);
            if($entreprise && ($entreprise->actif || $entreprise->getCreateur->idUser == Auth::id() || Auth::user()->isAdmin()))
                return view('entreprise/uneEntreprise',['entreprise'=>$entreprise]);
            return redirect(route('accueil'));
        }
        return redirect(route('login'));
    }

    function afficherToutes(){
        if (Auth::check()){
            $entreprises = DB::table("entreprise")->where('actif',1)->get();
            return view('entreprise/toutesEntreprises',['entreprises'=>$entreprises]);
        }
        return redirect(route('login'));
    }

}
