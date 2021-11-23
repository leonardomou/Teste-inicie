<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class dadosController extends Controller
{
    
  
    public function getDados($state, $dataInicial, $dataFinal )
    {
        
        $dataInicial = $this->dateSubstituteslash($dataInicial);
        $dataFinal = $this->dateSubstituteslash($dataFinal); 

        try {
            $top10 = [];   

            $response = Http::withHeaders(
                [
                    'User-Agent' => 'teste',
                    'Authorization'=> 'token cd06accc7cba9e0b48b4d3106f3ea4359f593725' // Aqui o mais correto é configurar o token no .env por questões de segurança
                ])->get ('https://api.brasil.io/dataset/covid19/caso/data',[
                    'state'=> $state,
                    'date' => $dataInicial
                ]);    

                $cidades = $response->json()['results'];

                foreach ($cidades as $i => $cidade) { 
                    
                    if ( ($cidade['confirmed'] > 0) && (($cidade['estimated_population_2019'] > 0))) {
                        $percentual = ($cidade['confirmed'] / $cidade['estimated_population_2019']) * 100;
                    }               
                        array_push($cidades[$i],  $percentual );             
                    
                }
                usort($cidades, array($this,'compare'));

                for ($a=0 ; $a < 10 ; $a++) {
                    
                    array_push($top10, $cidades[$a]);
                }
               
                return $top10;
           
        }catch (Exception $e) {
            return $e;
        }        
    }

    private static function compare($a, $b)
    {
    
        return $a['0'] < $b['0'];
    }    

    public function sendTop10(Request $request)
    {
        $top10 = $this->getDados($request->state, $request->dataInicial, $request->dataFinal);
        $cidades = [];
        foreach ($top10 as $i => $cidade) {
            array_push($cidades, ['nomeCidade' => $cidade['city'], 'percentualDeCasos' => $cidade['0']]);
        }

        try {
            $response = Http::withHeaders(
                [
                            'MeuNome' => 'Leonardo de Moura Soares',               
                        ])->post ('https://us-central1-lms-nuvem-mestra.cloudfunctions.net/testApi',[
                            'id'=> $cidades               
                        ]); 
                        
            return $response;
        }catch (Exception $e) {
            return $e;
        }

        
    }

    // SUBSTITUIR BARRAS POR TRAÇOS NA DATA
    public static function dateSubstituteslash($date)
    {
        return implode("-",array_reverse(explode("/",$date)));
    }
    public static function dateBrazilian($date)
    {
        return implode("/",array_reverse(explode("-",$date)));
    }
}
