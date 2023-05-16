#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc<4)
  {
    print "\n\nUsage:\n";
    print "  show_ancestors.php family_tree_file person_index generation_depth\n\n";

    print "Examples:\n";
    print "  ./show_ancestors.php FamilyTree.txt 33 4\n\n";

    print "  Explain what this script does here.\n\n";

    print "  The family_tree file contains text columns delimited by pipe characters.  There is a specific format that must\n";
    print "  be used and is described in the 'Notes.biol.txt' file.\n\n";
    exit(0);
  }

  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[1];
  $target_id = $argv[2];
  $gen_depth = $argv[3];

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);

//--------------------------------------------------------------------------------------

  if(!is_numeric($target_id)) { $target_id = GetIDForFullName($target_id,$lines); }

//--------------------------------------------------------------------------------------

  $ancestors = ExtractAncestorDataForID($lines,$target_id,$gen_depth);

  print "\n\n----------- Generation: 1 ----------\n";
  $str = GetNameStringFromDataLine($ancestors[0][0]);
  print "$str\n";

  for($g=1;$g<$gen_depth;++$g)
  {
    $gg = $g+1;  
    print "----------- Generation: $gg ----------\n";
    for($i=0;$i<pow(2,$g);$i=$i+2)
    {
      $str = GetNameStringFromDataLine($ancestors[$g][$i])."  [".GetBirthDeathYearStringFromDataLine($ancestors[$g][$i])."]";
      print "$str\n";
      $str = GetNameStringFromDataLine($ancestors[$g][$i+1])."  [".GetBirthDeathYearStringFromDataLine($ancestors[$g][$i+1])."]";
      print "$str\n";
    }
  }
  print "-----------------------------------\n\n";

?>
