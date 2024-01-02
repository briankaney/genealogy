#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  if($argc<2)
  {
    print "\n\nUsage:\n";
    print "  convert_people_file_to_json.php family_tree_file\n\n";

    print "Examples:\n";
    print "  ./convert_people_file_to_json.php Kaney.people.txt\n\n";

    print "  This scripts reads the contents of a people file.  A people file contains pipe\n";
    print "  delimited text columns in a specific format described in the 'FormatNotes.people.txt' file.\n";
    print "  This scripts converts all the data to a json.\n\n";

    exit(0);
  }

//  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the input file name and test to see if it exists.
//--------------------------------------------------------------------------------------

  $infile = $argv[1];

  if(!file_exists($infile))
  {
    print "\nInput file not found: $infile\n\n";
    exit(0);
  }

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//--------------------------------------------------------------------------------------
//Kaney|Edwin Frederick||M|2|6|7|09/13/1882|06/22/1950
//Kaney|Henry|Jr|M|6|8|9|12/25/1848|10/11/1888
//Kaney|Henry|Sr|M|8|0|0|03/09/1817|08/21/1899
//Klemme|Anna Marie Elisabeth||F|1060|1065|1066|12/?/1755|03/07/1811
//--------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

  $last_name = Array();
  $first_name = Array();
  $name_suffix = Array();
  $gender = Array();
  $index = Array();
  $father_id = Array();
  $mother_id = Array();
  $birth_date = Array();
  $death_date = Array();

  for($i=0;$i<$num_lines;++$i)
  {
    $fields = explode('|',$lines[$i]);      

    $last_name[$i] = $fields[0];
    $first_name[$i] = $fields[1];
    $name_suffix[$i] = $fields[2];
    $gender[$i] = $fields[3];
    $index[$i] = $fields[4];
    $father_id[$i] = $fields[5];
    $mother_id[$i] = $fields[6];
    $birth_date[$i] = $fields[7];
    $death_date[$i] = $fields[8];
  }
  
 //--------------------------------------------------------------------------------------

  print "{";
  writeJSONArrayBlock("LastName",$last_name,"string");
  print ",";
  writeJSONArrayBlock("FirstName",$first_name,"string");
  print ",";
  writeJSONArrayBlock("NameSuffix",$name_suffix,"string");
  print ",";
  writeJSONArrayBlock("Gender",$gender,"string");
  print ",";
  writeJSONArrayBlock("Index",$index,"number");
  print ",";
  writeJSONArrayBlock("FatherID",$father_id,"number");
  print ",";
  writeJSONArrayBlock("MotherID",$mother_id,"number");
  print ",";
  writeJSONArrayBlock("BirthDate",$birth_date,"string");
  print ",";
  writeJSONArrayBlock("DeathDate",$death_date,"string");
  print "}";

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function writeJSONArrayBlock($name,$array,$type)
  {
    print "\"$name\":[";

    $cnt = count($array);
    if($cnt==0) { print "]"; }

    if($type=='number')
    {
      for($i=0;$i<$cnt;++$i)
      {
        print "$array[$i]";
        if($i<$cnt-1) { print ","; }
        else          { print "]"; }
      }
    }
    if($type=='string')
    {
      for($i=0;$i<$cnt;++$i)
      {
        print "\"$array[$i]\"";
        if($i<$cnt-1) {  print ","; }
        else          { print "]"; }
      }
    }
  }

?>
