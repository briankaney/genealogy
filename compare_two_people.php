#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc==1)
  {
    print "\n\nUsage:\n";
    print "  compare_two_people.php family_tree_file index_person1 index_person2\n\n";

    print "Examples:\n";
    print "  ./compare_two_people.php FamilyTree.txt 33 1\n\n";

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
  $poi_id = $argv[2];
  $doe_id = $argv[3];
  $delimiter = "pipe";

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
//1009|Abdella_Michael Theodore_|Abdella|Michael Theodore||M|||Born|0|07/27/1990|447|1007|Died|?|?/?/?|
//1008|Abdella_Ryan Howard_|Abdella|Ryan Howard||M|333||Born|0|07/27/1990|447|1007|Died|?|?/?/?|
//1010|Abdella_Scott John_|Abdella|Scott John||M|||Born|0|09/12/1991|447|1007|Died|?|?/?/?|
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);

//--------------------------------------------------------------------------------------

  $person1 = ExtractSelfDataForID($lines,$poi_id);
  $person2 = ExtractSelfDataForID($lines,$doe_id);

  print "\n$person1\n$person2\n\n";

//  $gender = GetGenderFromData($self);
//  $str = GetNameStringFromData($self)." ".$gender;
//  print "\nSelf: $str\n\n";

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  $poi = ExtractAncestorDataForID($lines,$poi_id,10);
  $doe = ExtractAncestorDataForID($lines,$doe_id,10);

  $num_pgen = count($poi);
  $num_dgen = count($doe);

  $last_gen = false;

//  for($g=0;$g<4;++$g)
  for($g=0;$g<$num_pgen;++$g)
  {
    $num_p = count($poi[$g]);
    for($i=0;$i<$num_p;++$i)
    {
//      for($h=$g;$h<$num_dgen;++$h)
      for($h=0;$h<$num_dgen;++$h)
      {
        $num_d = count($doe[$h]);
        for($j=0;$j<$num_d;++$j)
        {
          if($poi[$g][$i]==$doe[$h][$j] && GetIDFromDataLine($poi[$g][$i])!=0)
	  {
            $m = min($g,$h);
            $d = abs($g-$h);

	    $str = "Match Found: R = ".$g.", S = ".$h."\n             M = ".$m.", D = ".$d;
	    print "$str\n\n";
	    if($g==0 && $h==0) { $str = "Self:  ".$poi[$g][$i]; }
	    if($g==1 && $h==1) { $str = "Sibling:  ".$poi[$g][$i]; }

            if($m==1 && $d==1) { $str = "Uncle or Aunt/Nephew or Niece:  ".$poi[$g][$i]; }
	    if(($m==0 && $d==1) || ($g==1 && $h==0)) { $str = "Parent/Child:  ".$poi[$g][$i]; }
	    if($m==0 && $d==2) { $str = "Grandparent/Grandchild:  ".$poi[$g][$i]; }
	    if($m==0 && $d>2)
	    {
	      $str = "";
              for($gr=0;$gr<$d-2;++$gr) { $str = $str."G"; }
	      $str = $str." Grandparent/Grandchild:  ".$poi[$g][$i];
            }
	    if($m>=2) { $str = "Cousin Level:  ".($m-1).",  Removal:  ".$d.",  ".$poi[$g][$i]; }
	    print "$str\n\n";
//	    return 0;
            $last_gen = true;
	  }
        }
      }
    }
    if($last_gen) { return 0; }
  }



?>
