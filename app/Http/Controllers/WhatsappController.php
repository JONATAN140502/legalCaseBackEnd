<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Uuid;
use Twilio\TwiML\MessagingResponse;

class WhatsappController extends Controller
{    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request) {
       $sid = "AC9889a787b80d3df409ea6b9b55c65df8";
       $token = "820102ae37c8ba500ca347cab9f77c1b";
       $twilio = new Client($sid, $token);
    //para enviar mensajes de texto 
       $message = $twilio->messages
       ->create("+51919614613", // to
         array(
           "from" => "+15614199187",
           "body" =>$request->mensaje
         )
       );
   ///para enviar mensaje a whatsapp
    // $message = $twilio->messages
    //   ->create("whatsapp:+51919614613", // to
    //     array(
    //       "from" => "whatsapp:+14155238886",
    //       "body" => $request->mensaje
    //     )
    //   );
    return response()->json(['mensaje'=>$message->sid]);
    }
}
