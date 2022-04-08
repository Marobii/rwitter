<?php

namespace App\Http\Controllers;

use App\Models\UserActivity as ModelsUserActivity;
use Illuminate\Http\Request;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Mod;
use Symfony\Component\Console\Logger\ConsoleLogger;

class RwitterController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {

            $qas = ModelsUserActivity::orderBy('updated_at', 'desc')->get();
            return view('/rwitterhome',compact('qas'));

    }

    //Store
    public function store(Request $request)
    {
        $paper = new ModelsUserActivity();

        $paper->message = request('message');

        $lastinsertedid = ModelsUserActivity::max('id');

        !empty($lastinsertedid) ? $lastposition = $lastinsertedid : $lastposition = 0;

        $newposition = $lastposition + 1;

        $paper->position = $newposition;

        $msg = $_POST['message'];

        /* Verifica se o rwitt é null */
        if ($msg == "") {
            /* Informa que não é possível escrever rwitt em branco */
            return redirect('/rwitterhome')->with('info', 'Escreve um Rwitt');
        } else {
            /* Informa que o rwitt foi publicado */
            $paper->save();

            return redirect('/rwitterhome')->with('info', 'Rwitter enviado com sucesso');
        }

    }

    /* Apaga da db de acordo com o id */
    public function destroy($id){
        $bb = ModelsUserActivity::findOrFail($id);
        $bb->delete();

        return redirect('/rwitterhome');
    }

    public function funfat($id){
        $receive = null;
        /* Indica posição do id */
        $currentposition = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();


        /* Verificar todas posições */
        $checkallpositions = ModelsUserActivity::select('position')
            ->get();
            /* error_log($checkallpositions);
            error_log($currentposition);
            error_log($checkallpositions[3]['position']); */

            /* error_log($currentposition); */

        /* Sabe a posição deste id */
        $comp = $currentposition[0]['position'];
        error_log($comp);

        /* Mostra todas as posições */
        foreach($checkallpositions as $index=>$opt){
            /* Compara se existe maior */
            if($checkallpositions[$index]['position'] > $comp ){
                /* recebe posição acima da atual */
                $receive = $checkallpositions[$index]['position'];
                break;
            } else {
                error_log("nada maior");
            }
        }

        /* calcular se existe posição acima da atual */
        /* se existir trocar com temp valv */

        return redirect('/rwitterhome')->with('info', $receive);
    }

}
