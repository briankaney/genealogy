#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------------
//   If run with no command line args, print out usage statement and bail
//--------------------------------------------------------------------------------------

  $argc = count($argv);

  if($argc<3)
  {
    print "\n\nUsage:\n";
    print "  show_summary.php family_tree_file person_index\n\n";

    print "Examples:\n";
    print "  ./show_summary.php FamilyTree.txt 33\n\n";

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

  $self = ExtractSelfDataForID($lines,$target);

  $gender = GetGenderFromDataLine($self);
  $str = GetNameStringFromDataLine($self)." ".$gender;
  print "\nSelf: $str\n\n";

  $self_born = GetBirthDateStringFromDataLine($self);
  $self_died = GetDeathDateStringFromDataLine($self);
  $age = GetWholeYearsPassed($self_born,$self_died);

  print "$self_born $self_died [$age]\n\n";

//--------------------------------------------------------------------------------------

  $parents = ExtractParentDataForID($lines,$target);
  $str = GetNameStringFromDataLine($parents[0])." [".GetIDFromDataLine($parents[0])."]";
  $str = $str."   ".GetNameStringFromDataLine($parents[1])." [".GetIDFromDataLine($parents[1])."]";
  print "\nParents: $str\n";
 	  
  $father_died = GetDeathDateStringFromDataLine($parents[0]);
  $lose_father_age = GetWholeYearsPassed($self_born,$father_died);
  $mother_died = GetDeathDateStringFromDataLine($parents[1]);
  $lose_mother_age = GetWholeYearsPassed($self_born,$mother_died);
  print "Age when parents deceased: $lose_father_age     $lose_mother_age\n\n";

//--------------------------------------------------------------------------------------

  $siblings = ExtractSiblingDataForID($lines,$target,"suppress_self");
  $number_sibs = count($siblings);

  print "\nBiological Siblings:\n  Number: $number_sibs\n\n";

  for($i=0;$i<$number_sibs;++$i)
  {
    $str = "  ".GetNameStringFromDataLine($siblings[$i]);
//    if($gender=="M") { $str = $str."  (with ".GetMotherIDFromData($children[$i]).")"; }
//    if($gender=="F") { $str = $str."  (with ".GetFatherIDFromData($children[$i]).")"; }
    print "$str\n";
  }

//--------------------------------------------------------------------------------------

/*  
  $father_id = $fields[$self_index][11];
  $mother_id = $fields[$self_index][12];

  print "Siblings:\n";
  for($i=0;$i<$num_lines;++$i)
  {
    if($father_id<=0) { print "Father not found - sibling search failed\n";  break; }	    
    if($mother_id<=0) { print "Mother not found - sibling search failed\n";  break; }	    

    if($fields[$i][1]==$self) { continue; }
    $sibling_str = $fields[$i][1]." [".$fields[$i][0]."]";

    if($father_id==$fields[$i][11] && $mother_id!=$fields[$i][12])
    {
      print "  $sibling_str - Half (with Father)\n";
    }

    if($father_id!=$fields[$i][11] && $mother_id==$fields[$i][12])
    {
      print "  $sibling_str - Half (with Mother)\n";
    }

    if($father_id==$fields[$i][11] && $mother_id==$fields[$i][12])
    {
      print "  $sibling_str - Full\n";
    }
  }
 */

//--------------------------------------------------------------------------------------

  $children = ExtractChildrenDataForID($lines,$target);
  $number_kids = count($children);
	
  print "\nBiological Children:\n  Number: $number_kids\n\n";

  for($i=0;$i<$number_kids;++$i)
  {
    $str = "  ".GetNameStringFromDataLine($children[$i]);
    if($gender=="M") { $str = $str."  (with ".GetMotherIDFromDataLine($children[$i]).")"; }
    if($gender=="F") { $str = $str."  (with ".GetFatherIDFromDataLine($children[$i]).")"; }
    print "$str\n";
  }

//--------------------------------------------------------------------------------------

  $grand_parents = ExtractGrandParentDataForID($lines,$target);

  $str = GetNameStringFromDataLine($grand_parents[0])." [".GetIDFromDataLine($grand_parents[0])."]";
  $str = $str."   ".GetNameStringFromDataLine($grand_parents[1])." [".GetIDFromDataLine($grand_parents[1])."]";
  print "\nPaternal Grandparents: $str\n";
  $str = GetNameStringFromDataLine($grand_parents[2])." [".GetIDFromDataLine($grand_parents[2])."]";
  $str = $str."   ".GetNameStringFromDataLine($grand_parents[3])." [".GetIDFromDataLine($grand_parents[3])."]";
  print "Maternal Grandparents: $str\n\n";



?>
