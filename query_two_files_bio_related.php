#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc<2)
  {
    print "\n\nUsage:\n";
    print "  query_two_files_bio_related.php family_tree_file\n\n";

    print "Examples:\n";
    print "  ./query_two_files_bio_related.php Kaney.biotree.txt Stenglin.pod.txt\n\n";

    print "  This scripts reads the contents of two biotree files.  A biotree file contains pipe\n";
    print "  delimited text columns in a specific format described in the 'FormatNotes.biotree.txt' file.\n";
    print "  This scripts determines if anyone in file 1 is biologically related to anyone in file 2.\n";
    print "  Both files are looped through and checked against everyone in the other file.  The check\n";
    print "  must be done in both directions since children must be found via the 'back-door', i.e. by\n";
    print "  asking if anyone has that person as a parent.\n\n";

    exit(0);
  }

  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the input file names and test to see that they both exist.
//--------------------------------------------------------------------------------------

  $infile1 = $argv[1];
  $infile2 = $argv[2];

  if(!file_exists($infile1))
  {
    print "\nInput file not found: $infile1\n\n";
    exit(0);
  }

  if(!file_exists($infile2))
  {
    print "\nInput file not found: $infile2\n\n";
    exit(0);
  }

//--------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines1 = file("$infile1",FILE_IGNORE_NEW_LINES);
  $num_lines1 = count($lines1);

  $lines2 = file("$infile2",FILE_IGNORE_NEW_LINES);
  $num_lines2 = count($lines2);

//--------------------------------------------------------------------------------------
//   Loop through lines and find any people that have no parents or children in the file.  
//--------------------------------------------------------------------------------------

  $files_related = false;

  for($i=0;$i<$num_lines1;++$i)
  {
    $id = GetIDFromDataLine($lines1[$i]);
    if($id==0) { continue; }

    for($j=0;$j<$num_lines2;++$j)
    {
      $test_father_id = GetFatherIDFromDataLine($lines2[$j]);
      $test_mother_id = GetMotherIDFromDataLine($lines2[$j]);  

      if($test_mother_id==$id || $test_father_id==$id) { $files_related = true;  print "File 1: $id\n";}
    }
  }

  if($files_related==true) { print "\n\nFiles $infile1 and $infile2 are biologically related\n\n";  exit(0); }

  for($i=0;$i<$num_lines2;++$i)
  {
    $id = GetIDFromDataLine($lines2[$i]);
    if($id==0) { continue; }

    for($j=0;$j<$num_lines1;++$j)
    {
      $test_father_id = GetFatherIDFromDataLine($lines1[$j]);
      $test_mother_id = GetMotherIDFromDataLine($lines1[$j]);  

      if($test_mother_id==$id || $test_father_id==$id) { $files_related = true;  print "File 2: $id\n"; }
    }
  }

  if($files_related==true)  { print "\n\nFiles $infile1 and $infile2 are biologically related\n\n"; }
  if($files_related==false) { print "\n\nFiles $infile1 and $infile2 have no biological relationships\n\n"; }
 
?>
