<?php

namespace App\Http\Controllers;

use App\models\Quote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Item;
use App\models\User;
use App\models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth ;
use App\models\ActivityLog;
use Carbon\Carbon;

class QuoteController extends Controller
{
     // create new Quote By admin
     public function create(Request $request)
     {
         $quote = new quote($request->quote);
         $quote->quoteNum = $request->config['quoteNum'] ;
         $quote->client_id = $request->config['clientId'] ;
         $quote->fiscal_timber = $request->config['tax'] ;
         $quote->rate_tva = $request->config['tva'] ;
         $quote->save();
         $quote = $quote->id  ;
         $items = $request->items;
       foreach($items as $item)
       {
             $i = new Item($item);
             $i->quote_id = $quote_id ;
             $i->save();
       }

       return $quote ;
          return response()->json(['message'=>'created']) ;
     }

     // update quote by user&admin
    public function update(Request $request, $id)
    {
        //update quote_id and save it
        $quote = Quote::find($id) ;
        $quote->update($request->quote);
        $quote->save();

        //delete old items
        $quote_id= $quote->id;
        $items =  Item::where('quote_id',$quote_id)->get();
        foreach($items as $item)
        {
              $quote = $quote->id;
              $items = Item::where('quote_id',$quote_id)->delete();
        }

        //save new item
        $items = $request->items;
        foreach($items as $item)
        {
              $i = new Item($item);
              $i->quote_id = $quote_id ;
              $i->save();
        }
        return response()->json(['message'=>'updated']) ;
    }

     //get all quote for admin
     public function getAllquotes()
     {
         $quotes = Quote::with('client')->get() ;

         return response()->json(["quotes"=> $quotes ]);
     }

     // get quote By id
     public function getquoteById($id)
        {
            $quote = Quote::find($id);
            $items= Item::where('quote_id',$id)->get();
            return response()->json(["quote"=> $quote, "items"=>$items]);
        }
    // delete quote by admin
     public function delete(Request $request)

     {
         $table = $request->quotes_id;
         foreach ($table as $t)
         {
             $id= ($t['quote_id']);
             $quote = Quote::find($id);
             $quote->delete();
         }
         return response()->json(['message'=>'Deleted']) ;
     }

     //Calculate quotes number
     public function calcNumquotes(Request $request){
        $quotes = Quote::all() ;
        $thisYearquote = array();
        $year = $request->year;
        foreach ($quotes as $quote) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $quote->DateFacturation);
        $quoteYear = $date->format('Y');
        if ($quoteYear == $year) {
            array_push($thisYearquote,$quote);
        }
        }
        return response()->json(["numquote"=>count($thisYearquote)]);
    }
}
