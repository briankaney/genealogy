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
    print "  ./compare_two_people.php Kaney.people.txt 'Paul Eugene Kaney' 'Brian Todd Kaney'\n";
    print "  ./compare_two_people.php -mode=index Kaney.people.txt 33 1\n\n";

    print "  This script takes a 'people' file and two individuals and determines what (if any)\n";
    print "  the relationship between them might be.  The two individuals can be entered in two\n";
    print "  ways.  The default is the full name string for each person.  The other requires\n";
    print "  using the '-mode=index' option switch followed by the numerical person index for\n";
    print "  each individual.\n\n";

    print "  Default delimiter is the pipe character, but can be changed via '-d=spaces' or '-d=comma'.  If 'spaces' is used\n";
    print "  file read and write may not be symmetric in that reading counts any number of consecutive spaces as a single\n";
    print "  delimiter, but always uses one a one space delimiter in the output.\n\n";
    exit(0);
  }

  include 'library_ancestory.php';

//--------------------------------------------------------------------------------------
//   Read the required args and make sure the one that is the input is a file that 
//   exists.  Read optional args for number of header lines and delimiter character.
//--------------------------------------------------------------------------------------

  $mode = "name";
  for($i=1;$i<=$argc-4;++$i)
  {
    if($argv[$i]=="-mode=index") { $mode = "index"; }
  }

  $infile = $argv[$argc-3];
  $poi_arg = $argv[$argc-2];
  $doe_arg = $argv[$argc-1];

//--------------------------------------------------------------------------------------
//   Read in contents of input file
//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------
//1009|Abdella_Michael Theodore_|Abdella|Michael Theodore||M|||Born|0|07/27/1990|447|1007|Died|?|?/?/?|
//1008|Abdella_Ryan Howard_|Abdella|Ryan Howard||M|333||Born|0|07/27/1990|447|1007|Died|?|?/?/?|
//1010|Abdella_Scott John_|Abdella|Scott John||M|||Born|0|09/12/1991|447|1007|Died|?|?/?/?|
//--------------------------------------------------------------------------------------

  $lines = file("$infile",FILE_IGNORE_NEW_LINES);

  if($mode=="index")
  {
    $poi_id = $poi_arg;
    $doe_id = $doe_arg;
  }
  if($mode=="name")
  {
    $poi_index = GetDataLineIndexForFullName($lines,$poi_arg);
    $doe_index = GetDataLineIndexForFullName($lines,$doe_arg);
    if($poi_index==-1) { print "Error: '$poi_arg' not found\n"; }
    if($doe_index==-1) { print "Error: '$doe_arg' not found\n"; }
    if($poi_index==-1 || $doe_index==-1) { return 0; }
    $poi_id = GetIDFromDataLine($lines[$poi_index]);
    $doe_id = GetIDFromDataLine($lines[$doe_index]);
  }

  $person1 = ExtractSelfDataForID($lines,$poi_id);
  $person2 = ExtractSelfDataForID($lines,$doe_id);

  print "\n$person1\n$person2\n\n";

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  $poi = ExtractAncestorDataForID($lines,$poi_id,10);
  $doe = ExtractAncestorDataForID($lines,$doe_id,10);

  $num_pgen = count($poi);
  $num_dgen = count($doe);

  $last_gen = false;

  for($g=0;$g<$num_pgen;++$g)
  {
    $num_p = count($poi[$g]);
    for($i=0;$i<$num_p;++$i)
    {
      for($h=0;$h<$num_dgen;++$h)
      {
        $num_d = count($doe[$h]);
        for($j=0;$j<$num_d;++$j)
        {
          if($poi[$g][$i]==$doe[$h][$j] && GetIDFromDataLine($poi[$g][$i])!=0)
	  {
            $m = min($g,$h);
            $d = abs($g-$h);

	    $str = "Match Found: R = ".$g.", S = ".$h."\n             Min(R,S) = ".$m.", |R-S| = ".$d;
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
	    if($m==1 && $d>1)
	    {
	      $str = "";
              for($gr=0;$gr<$d-1;++$gr) { $str = $str."G"; }
	      $str = $str." Uncle or Aunt/Nephew or Niece:  ".$poi[$g][$i];
            }
	    if($m>=2) { $str = "Cousin Level:  ".($m-1).",  Removal:  ".$d.",  ".$poi[$g][$i]; }
	    print "$str\n\n";
            $last_gen = true;
	  }
        }
      }
    }
    if($last_gen) { return 0; }
  }


?>
