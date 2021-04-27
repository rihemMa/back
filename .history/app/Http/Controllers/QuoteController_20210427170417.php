<?php

namespace App\Http\Controllers;

use App\models\Quote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\QuoteItems;
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
         $quote = new Quote($request->quote);
         $quote->quoteNum = $request->config['quoteNum'] ;
         $quote->client_id = $request->config['clientId'] ;
         $quote->fiscal_timber = $request->config['tax'] ;
         $quote->rate_tva = $request->config['tva'] ;
         $quote->save();
         $quote_id = $quote->id  ;
         $items = $request->items;
       foreach($items as $item)
       {
             $i = new QuoteItems($item);
             $i->quote_id = $quote_id ;
             $i->save();
       }

       return $quote ;
          return response()->json(['message'=>'created']) ;
     }

     // update quote by user&admin
    public function update(Request $request, $id)
    {
         $newQuote = $request->quote ;
         $items = $request->itsmes ;
         $config = $request->config ;
        $quote = Quote::find($id) ;
        $quote->update([
            "total_ttc" => $quote['total_ttc'] ,
            "ht_price" => $quote['ht_price'],
            "rate_tva" => $config['tva'],
            "price_tva" => $quote['price_tva'],
            "fiscal_timber" => $config['tax'],
            "quoteNum" => $config['quoteNum'],
            "DateFacturation" => $config['dateFacturation'],
            "description" => $quote['description'],
            "client_id" => $config['clientId'],
            "inWord" => $quote['inWord']
        ]);
        $bill->save();

        //delete old items
        $bill_id = $bill->id;
        $items =  Item::where('bill_id',$bill_id)->get();
        foreach($items as $item)
        {
              $bill_id = $bill->id;
              $items = Item::where('bill_id',$bill_id)->delete();
        }

        //save new item
        $items = $request->items;
        foreach($items as $item)
        {
              $i = new Item($item);
              $i->bill_id = $bill_id ;
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
            $items= QuoteItems::where('quote_id',$id)->get();
            return response()->json(["quote"=> $quote, "items"=>$items]);
        }

      // delete Bill by admin
      public function delete(Request $request)
      {
          $table = $request->quotes_id;
          foreach ($table as $t)
          {
              $id= ($t['quote_id']);
              $bill = Quote::find($id);
              $bill->delete();
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
