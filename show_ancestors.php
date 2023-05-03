#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  show_ancestors.php family_tree_file person_index\n\n";

    print "Examples:\n";
    print "  ./show_ancestors.php FamilyTree.txt 33\n\n";

    print "  Explain what this script does here.\n\n";

//--modify options as needed.
    print "Options:\n";
    print "  Use '-h=3' to specify a number of header lines.  The header section is output but not otherwise acted upon by\n";
    print "  by this script.  The default value is zero.\n\n";

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces' or '-d=comma'.  If 'spaces' is used\n";
    print "  file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses one a one space delimiter in the output.\n\n";
    exit(0);
  }

  include 'library_text_columns.php';
  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $infile = $argv[1];
  $target = $argv[2];
  $delimiter = "pipe";

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);

//--------------------------------------------------------------------------------------

//  $ancestors = ExtractAncestorDataForID($lines,$target,10);
  $ancestors = ExtractAncestorDataForID($lines,$target,6);

  $num_gen = count($ancestors);

  print "\n\n------------------------------------------\n\n";
  for($g=1;$g<$num_gen;++$g)
  {
    for($i=0;$i<pow(2,$g);$i=$i+2)
    {
      $str = GetNameStringFromDataLine($ancestors[$g][$i])."           ";
      $str = $str.GetNameStringFromDataLine($ancestors[$g][$i+1]);
//      print "$str\n";
      print "$str|";
    }
    print "\n------------------------------------------\n\n";
  }

//--------------------------------------------------------------------------------------

?>
