#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc<3)
  {
    print "\n\nUsage:\n";
    print "  show_descendants.php family_tree_file person_index\n\n";

    print "Examples:\n";
    print "  ./show_descendants.php FamilyTree.txt 33\n\n";

    exit(0);
  }

  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[1];
  $target = $argv[2];

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);


/*
  $num = count($test_array);

  print "$num\n\n";
  for($i=0;$i<$num;++$i) {
    print "$test_array[$i]\n";
  }
 */


//--------------------------------------------------------------------------------------

  if(!is_numeric($target)) { $target = GetIDForFullName($target,$lines); }

//--------------------------------------------------------------------------------------

  $descendants = ExtractDescendantDataForID($lines,$target,10);
  $num_gen = count($descendants);

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  $id_array = Array();
  $id_array = AppendIDsFromMultiGenDataLinesToUniqueIDArray($id_array,$descendants);

  for($g=1;$g<$num_gen;++$g)
  {      
    $num_in_last_gen = count($descendants[$g-1]);
    $num_in_gen = count($descendants[$g]);
    for($i=0;$i<$num_in_gen;++$i)
    { 
      $father = GetFatherIDFromDataLine($descendants[$g][$i]);
      $mother = GetMotherIDFromDataLine($descendants[$g][$i]);

      if(!IDIsInIDArray($id_array,$father) && $father>=1) {
	$id_array = AppendIDToUniqueIDArray($id_array,$father);
        $descendants[$g-1][$num_in_last_gen] = $lines[GetDataLineIndexForID($lines,$father)];
	++$num_in_last_gen;
      }
      if(!IDIsInIDArray($id_array,$mother) && $mother>=1) {
//print "$g  $mother  $num_in_last_gen\n\n";      
	$id_array = AppendIDToUniqueIDArray($id_array,$mother);
        $descendants[$g-1][$num_in_last_gen] = $lines[GetDataLineIndexForID($lines,$mother)];
	++$num_in_last_gen;
      }
    }
  }

//  print "\n\n------------------------------------------\n\n";
  for($g=0;$g<$num_gen;++$g)
  {
    $num_in_gen = count($descendants[$g]);
    for($i=0;$i<$num_in_gen;++$i)
    {
//      $str = GetNameStringFromDataLine($descendants[$g][$i]);
//      print "$str\n";

      $str = $descendants[$g][$i];
      print "$str\n";
    }
//    print "\n";
  }

?>
