#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc<2)
  {
    print "\n\nUsage:\n";
    print "  find_bio_singles.php family_tree_file\n\n";

    print "Examples:\n";
    print "  ./find_bio_singles.php Kaney.biotree.txt\n\n";

    print "  This scripts reads the contents of a biotree file.  A biotree file contains pipe\n";
    print "  delimited text columns in a specific format described in the 'FormatNotes.biotree.txt' file.\n";
    print "  This scripts searches through all the person lines and finds those who have no entries\n";
    print "  in the same file for either parents or children.  These individuals are not biologically\n";
    print "  related to anyone else within the file.  The fact that they are included most likely means\n";
    print "  they are a spouse of someone in the file, but the biotree file contains no info on marriages.\n\n";

    exit(0);
  }

  include 'library_ancestory.php';

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
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);
  $num_lines = count($lines);

//--------------------------------------------------------------------------------------
//   Loop through lines and find any people that have no parents or children in the file.  
//--------------------------------------------------------------------------------------

  for($i=0;$i<$num_lines;++$i)
  {
    $father_id = GetFatherIDFromDataLine($lines[$i]);
    $mother_id = GetMotherIDFromDataLine($lines[$i]);

    if($mother_id==0 && $father_id==0) {
      $test_id = GetIDFromDataLine($lines[$i]);
      $had_kids = false;
      for($j=0;$j<$num_lines;++$j)
      {
	if($i==$j) { continue; }
        $test_father_id = GetFatherIDFromDataLine($lines[$j]);
        $test_mother_id = GetMotherIDFromDataLine($lines[$j]);  
        if($test_mother_id==$test_id || $test_father_id==$test_id) { $had_kids = true; }
      }

      if($had_kids==false) { print "$lines[$i]\n"; }
    }	    
  }

?>
