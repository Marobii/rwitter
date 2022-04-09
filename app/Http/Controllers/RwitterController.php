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

            $qas = ModelsUserActivity::orderBy('position', 'desc')->get();
            return view('/rwitterhome',compact('qas'));

    }

    //Store
    public function store(Request $request)
    {
        $paper = new ModelsUserActivity();

        $paper->message = request('message');

        /* Pede a db o id maximo e armazena */
        $lastinsertedid = ModelsUserActivity::max('id');

        /* Ternário para saber se o último id é nulo, se for atribui 0 senão atribui o valor de $lastinsertedid */
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

    /* Move up */
    public function moveup($id){
        /* Receber informação do feed que queremos mover */
        $grabinfo = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();

        /* Retirar do array os valores do id que queremos mover e guardar nas respetivas variaveis */
        $grabid =  $grabinfo[0]['id'];
        $grabposition =  $grabinfo[0]['position'];

        /* Pedir a db a maior posição existente */
        $maxposition =  ModelsUserActivity::max('position');

        /* Pedir a db o id correspondente a maior posição existente */
        $maxid = ModelsUserActivity::select('id')
            ->where('position', $maxposition)
            ->get();


        /* Incrementar a posição do tweet selecionado */
        $confirmnext = $grabposition + 1;

        /* Verificar na base de dados se existe um id que corresponda a posição incrementada */
        $askdb = ModelsUserActivity::select('id')
            ->where('position', $confirmnext)
            ->get();

        /* Compara se a posição atual é igual a maior posição existente */
        if ($maxposition == $grabposition){
            /* Se for verdade ele não deixa  */
            error_log("Já estás no topo");
        }else {

            if(empty($askdb[0])){
                /* Pedir a base de dados todos os ids as posições que lhe correspondem ordenando por id de forma ascendente */
                $checkall=ModelsUserActivity::select('id' , 'position')
                ->orderBy('position', 'asc')
                ->get();

                /* Procurar no array em cada elemento */
                foreach($checkall as $index=>$opt){
                    /* Receber o valor da posição de acordo com o index do array */
                    $position = $checkall[$index]['position'];
                    if ($position === $grabposition){
                        error_log("faz nada");
                    }else {
                        if ($position > $grabposition ) {

                            /* Pedir a db o id que corresponde a variavel $position */
                            $idposition = ModelsUserActivity::select('id')
                            ->where('position', $position)
                            ->get();

                            /* Receber o id do array e armazenar numa nova variavel */
                            $storeid = $idposition[0]['id'];

                            /* Update nas posições na db */
                            ModelsUserActivity::where("id", $storeid)->update(["position" => $grabposition]);
                            ModelsUserActivity::where("id", $grabid)->update(["position" => $position]);

                            break;
                        }
                    }
                }

            } else {
                /* Incrementar a posição do tweet selecionado e armazenar numa nova variavel */
                $incposition = $grabposition + 1;

                /* Verificar na db o id da posição para a qual queremos mover o nosso tweet */
                $giveid = ModelsUserActivity::select('id')
                ->where('position', $incposition)
                ->get();

                /* Retirar do array o valor do id*/
                $idabove = $giveid[0]['id'];

                /* Verificar a posição do id para a qual queremos mover o nosso tweet */
                $giveposition = ModelsUserActivity::select('position')
                ->where('id', $idabove)
                ->get();

                /* Retirar do array a posição do id para a qual queremos mover o nosso tweet */
                $feedaboveposition = $giveposition[0]['position'];

                /* Update dos valores na db */
                ModelsUserActivity::where("id", $grabid)->update(["position" => $feedaboveposition]);
                ModelsUserActivity::where("id", $idabove)->update(["position" => $grabposition]);

                error_log("entrei");
                }
        }

        return redirect('/rwitterhome')->with('info');
    }

    /* Move Down */
    public function movedown($id){
        /* Receber informação do feed que queremos mover */
        $grabinfo = ModelsUserActivity::select('id', 'position')
            ->where('id',$id)
            ->get();

        /* Retirar do array os valores do id que queremos mover e guardar nas respetivas variaveis */
        $grabid =  $grabinfo[0]['id'];
        $grabposition =  $grabinfo[0]['position'];

        /* Pedir a db a menor posição existente */
        $minposition =  ModelsUserActivity::min('position');
        $minid = ModelsUserActivity::select('id')
            ->where('position', $minposition)
            ->get();

        /* Redução da posição do tweet selecionado */
        $confirmnext = $grabposition - 1;

        /* Verificar na base de dados se existe um id que corresponda a posição incrementada */
        $askdb = ModelsUserActivity::select('id')
            ->where('position', $confirmnext)
            ->get();

        /* Compara se a posição atual é igual a menor posição existente */
        if ($minposition == $grabposition){
            error_log("Já estás no fundo");
        }else {
            if(empty($askdb[0])){
                /* Pedir a base de dados todos os ids as posições que lhe correspondem ordenando por id de forma ascendente */
                $checkall=ModelsUserActivity::select('id' , 'position')
                ->orderBy('position', 'asc')
                ->get();

                /* Procurar no array em cada elemento */
                foreach($checkall as $index=>$opt){
                    /* Receber o valor da posição de acordo com o index do array */
                    $test = $checkall[$index]['position'];
                    if ($test === $grabposition){
                        error_log("faz nada");
                    }else {
                        error_log($checkall);
                        if ($test < $grabposition && $minposition != $test) {

                            /* Pedir a db o id que corresponde a variavel $position */
                            $redirect = ModelsUserActivity::select('id')
                            ->where('position', $test)
                            ->get();

                            /* Receber o id do array e armazenar numa nova variavel */
                            $storeid  = $redirect[0]['id'];

                            ModelsUserActivity::where("id", $storeid)->update(["position" => $grabposition]);
                            ModelsUserActivity::where("id", $grabid)->update(["position" => $test]);

                            break;
                        }
                    }
                }
            } else {
                /* Redução a posição do tweet selecionado e armazenar numa nova variavel */
                $temp = $grabposition - 1;

            /* Verificar na db o id da posição para a qual queremos mover o nosso tweet */
            $giveid = ModelsUserActivity::select('id')
            ->where('position', $temp)
            ->get();

            /* Retirar do array o valor do id*/
            $idabove = $giveid[0]['id'];

            /* Verificar a posição do id para a qual queremos mover o nosso tweet */
            $giveposition = ModelsUserActivity::select('position')
            ->where('id', $idabove)
            ->get();

            /* Retirar do array a posição do id para a qual queremos mover o nosso tweet */
            $feedaboveposition = $giveposition[0]['position'];

            /* Update dos valores na db */
            ModelsUserActivity::where("id", $grabid)->update(["position" => $feedaboveposition]);
            ModelsUserActivity::where("id", $idabove)->update(["position" => $grabposition]);

            error_log("entrei");
            }
        }
        return redirect('/rwitterhome')->with('info', $minposition);
    }
}
