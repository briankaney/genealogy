#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

//  include 'library_text_columns.php';
//  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//--------------------------------------------------------------------------------------

  $infile = "FamilyTree.10.txt";
  $delimiter = "pipe";

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//Behrmann|Wilhelmina||F|11|Wilhelmina Behrmann|0|0|48|01/26/1834|47|10/05/1906|
//Biesemeier|Frederick Simon||M|10|Frederick Simon Biesemeier|16|17|46|06/05/1822|47|10/25/1901|
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

  $gender = Array();
  $id = Array();
  $full_name = Array();
  $father_id = Array();
  $mother_id = Array();
  $birth_date = Array();
  $death_date = Array();

  for($i=0;$i<$num_lines;++$i)
  {
    $fields = explode('|',$lines[$i]);      

    $last_name[$i] = $fields[0];
    $first_name[$i] = $fields[1];
    $suffix[$i] = $fields[2];
    $gender[$i] = $fields[3];
    $id[$i] = $fields[4];
    $father_id[$i] = $fields[5];
    $mother_id[$i] = $fields[6];
    $birth_date[$i] = $fields[8];
    $death_date[$i] = $fields[10];
  }
  
 //--------------------------------------------------------------------------------------

  print "{";
  writeJSONArrayBlock("LastName",$last_name,"string");
  print ",";
  writeJSONArrayBlock("FirstName",$first_name,"string");
  print ",";
  writeJSONArrayBlock("Suffix",$suffix,"string");
  print ",";
  writeJSONArrayBlock("Gender",$gender,"string");
  print ",";
  writeJSONArrayBlock("ID",$id,"number");
  print ",";
  writeJSONArrayBlock("FatherID",$father_id,"number");
  print ",";
  writeJSONArrayBlock("MotherID",$mother_id,"number");
  print ",";
  writeJSONArrayBlock("BirthDate",$birth_date,"string");
  print ",";
  writeJSONArrayBlock("DeathDate",$death_date,"string");
  print "}";





/*
  $self = ExtractSelfDataForID($lines,$target);

  $gender = GetGenderFromData($self);
  $str = GetNameStringFromData($self)." ".$gender;
  print "\nSelf: $str\n\n";

//--------------------------------------------------------------------------------------

  $siblings = ExtractSiblingDataForID($lines,$target,"suppress_self");
  $number_sibs = count($siblings);

  print "\nBiological Siblings:\n  Number: $number_sibs\n\n";

  for($i=0;$i<$number_sibs;++$i)
  {
    $str = "  ".GetNameStringFromData($siblings[$i]);
    print "$str\n";
  }

//--------------------------------------------------------------------------------------

  $children = ExtractChildrenDataForID($lines,$target);
  $number_kids = count($children);
	
  print "\nBiological Children:\n  Number: $number_kids\n\n";

  for($i=0;$i<$number_kids;++$i)
  {
    $str = "  ".GetNameStringFromData($children[$i]);
    if($gender=="M") { $str = $str."  (with ".GetMotherIDFromData($children[$i]).")"; }
    if($gender=="F") { $str = $str."  (with ".GetFatherIDFromData($children[$i]).")"; }
    print "$str\n";
  }

//--------------------------------------------------------------------------------------

  $ancestors = ExtractAncestorDataForID($lines,$target,10);

  $num_gen = count($ancestors);

  print "\n\n------------------------------------------\n\n";
  for($g=1;$g<$num_gen;++$g)
  {
    for($i=0;$i<pow(2,$g);$i=$i+2)
    {
      $str = GetNameStringFromData($ancestors[$g][$i])."           ";
      $str = $str.GetNameStringFromData($ancestors[$g][$i+1]);
      print "$str\n";
    }
    print "\n";
  }
*/


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
