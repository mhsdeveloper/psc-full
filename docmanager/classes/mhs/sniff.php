<?php

    /*
        Simple class for troubleshooting code values  without resorting to die($value);

        Define a global constant SNIFF_OUT_FILE which is the file to output to

      */

	  namespace MHS;


      class Sniff {


          static function reset(){
              if(!defined("SNIFF_OUT_FILE")) die("please define SNIFF_OUT_FILE");
              $datestr = date("H:i:s - l, F j, Y");

              $out = "Starting to sniff at " . $datestr;
              $out .= "\n------------------------------------------------------------\n";

              @file_put_contents(SNIFF_OUT_FILE, $out);
          }




          static function out($string){
              if(!defined("SNIFF_OUT_FILE")) die("please define SNIFF_OUT_FILE");

              	$string .= "\n";

                @file_put_contents(SNIFF_OUT_FILE, $string, FILE_APPEND);
                return true;
          }


          static function print_r($what){
              if(!defined("SNIFF_OUT_FILE")) die("please define SNIFF_OUT_FILE");

              $out = print_r($what, true);

              @file_put_contents(SNIFF_OUT_FILE, $out, FILE_APPEND);
              return true;
          }

      }
