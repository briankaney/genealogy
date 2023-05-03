#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  create_biotree_spreadsheet.php family_biotree_file\n\n";

    print "Examples:\n";
    print "  create_biotree_spreadsheet.php Kaney.biotree.txt\n\n";

    exit(0);
  }

  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//--------------------------------------------------------------------------------------

  $infile = $argv[1];

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

  for($i=0;$i<$num_lines;++$i)
  {
    $gender = GetGenderFromDataLine($lines[$i]);
    $id = GetIDFromDataLine($lines[$i]);
    $full_name = GetNameStringFromDataLine($lines[$i]);
    $lived = GetBirthDeathYearStringFromDataLine($lines[$i]);


    $out_str = $id."\t".$full_name."\t".$gender."\t".$lived;
    print "$out_str\n";



//  print "{";
//  writeJSONArrayBlock("FatherID",$father_id,"number");
//  writeJSONArrayBlock("DeathDate",$death_date,"string");
//  print "}";



  }

?>
