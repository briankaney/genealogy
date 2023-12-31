#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc<2)
  {
    print "\n\nUsage:\n";
    print "  find_those_without_parents.php family_tree_file\n\n";

    print "Examples:\n";
    print "  ./find_those_without_parents.php Kaney.people.txt\n\n";

    print "  This scripts reads the contents of a people file.  A people file contains pipe\n";
    print "  delimited text columns in a specific format described in the 'FormatNotes.people.txt'\n";
    print "  file.  This script searches through all the person lines and finds those who have\n";
    print "  a missing entry in one or both parent IDs.\n\n";

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

    if($mother_id==-1 && $father_id>=0)  { print "no mother: $lines[$i]\n"; }
    if($mother_id>=0 && $father_id==-1)  { print "no father: $lines[$i]\n"; }
    if($mother_id==-1 && $father_id==-1) { print "no parent: $lines[$i]\n"; }
  }

?>
