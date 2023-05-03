<?php

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function GetLastNameFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[0];
  }

  function GetFirstMiddleNamesFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[1];
  }

  function GetNameSuffixFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[2];
  }

  function GetGenderFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[3];
  }

  function GetIDFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[4];
  }

  function GetNameStringFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);

    $full_name = GetFirstMiddleNamesFromDataLine($line)." ".GetLastNameFromDataLine($line); 

    $suffix = GetNameSuffixFromDataLine($line);
    if($suffix!="") { $full_name = $full_name." ".GetNameSuffixFromDataLine($line); }

    return $full_name;
  }

  function GetFatherIDFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[5];
  }

  function GetMotherIDFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[6];
  }

  function GetBirthLocationFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[7];
  }

  function GetBirthDateStringFromDataLine($line)
  {
    if($line=="") { return "?/?/?"; }
    $fields = explode('|',$line);
    return $fields[8];
  }

  function GetDeathLocationFromDataLine($line)
  {
    if($line=="") { return ""; }
    $fields = explode('|',$line);
    return $fields[9];
  }

  function GetDeathDateStringFromDataLine($line)
  {
    if($line=="") { return "?/?/?"; }
    $fields = explode('|',$line);
    return $fields[10];
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function GetBirthYearFromDataLine($line)
  {
    $str = GetBirthDateStringFromDataLine($line);
    $fields = explode('/',$str);
    return $fields[2];
  }	  
		
  function GetDeathYearFromDataLine($line)
  {
    $str = GetDeathDateStringFromDataLine($line);
    $fields = explode('/',$str);
    return $fields[2];
  }	  

  function GetBirthDeathYearStringFromDataLine($line)
  {
    return GetBirthYearFromDataLine($line)."-".GetDeathYearFromDataLine($line);
  }

  function GetYearsLivedDataLine($line)
  {
  }
	  
//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function ExtractSelfDataForID($lines,$person_id)
  {
    $line_count = count($lines);

    $self_line = "";
    for($i=0;$i<$line_count;++$i) {
      if(GetIDFromDataLine($lines[$i])==$person_id) { $self_line=$lines[$i]; break; }
    }
    return $self_line;
  }

  function ExtractParentDataForID($lines,$person_id)
  {
    $parent_data = Array();

    $self_line = ExtractSelfDataForID($lines,$person_id);
    $parent_data[0] = ExtractSelfDataForID($lines,GetFatherIDFromDataLine($self_line));
    $parent_data[1] = ExtractSelfDataForID($lines,GetMotherIDFromDataLine($self_line));

    return $parent_data;
  }

  function ExtractGrandParentDataForID($lines,$person_id)
  {
    $grand_parent_data = Array();

    $parents = ExtractParentDataForID($lines,$person_id);
    $grand_parent_data[0] = ExtractSelfDataForID($lines,GetFatherIDFromDataLine($parents[0]));
    $grand_parent_data[1] = ExtractSelfDataForID($lines,GetMotherIDFromDataLine($parents[0]));
    $grand_parent_data[2] = ExtractSelfDataForID($lines,GetFatherIDFromDataLine($parents[1]));
    $grand_parent_data[3] = ExtractSelfDataForID($lines,GetMotherIDFromDataLine($parents[1]));

    return $grand_parent_data;
  }

  function ExtractChildrenDataForID($lines,$person_id)
  {
    $children_data = Array();      
    $j=0;

    $line_count = count($lines);
    for($i=0;$i<$line_count;++$i)
    {
      if($person_id==GetFatherIDFromDataLine($lines[$i]) || $person_id==GetMotherIDFromDataLine($lines[$i])) {
        $children_data[$j] = $lines[$i];
        ++$j;
      }
    }

    return $children_data;
  }

  function ExtractSiblingDataForID($lines,$person_id,$mode)
  {
    $sibling_data = Array();      
    $j=0;

    $self_line = ExtractSelfDataForID($lines,$person_id);
    $father_id = GetFatherIDFromDataLine($self_line);
    $mother_id = GetMotherIDFromDataLine($self_line);

    $line_count = count($lines);
    for($i=0;$i<$line_count;++$i)
    {      
      if(GetIDFromDataLine($lines[$i])==$person_id && $mode=="suppress_self") { continue; }
      if($father_id==-1 || $mother_id==-1) { continue; }
      if($father_id==GetFatherIDFromDataLine($lines[$i]) || $mother_id==GetMotherIDFromDataLine($lines[$i]))
      {
        $sibling_data[$j] = $lines[$i];
        ++$j;
      }
    }
    return $sibling_data;
  }

//--------------------------------------------------------------------------------------

  function ExtractAncestorDataForID($lines,$person_id,$max_depth)
  {
    $ancestor_data = Array();
    $ancestor_data[0] = Array();
    $ancestor_data[0][0] = ExtractSelfDataForID($lines,$person_id);

    for($g=1;$g<=$max_depth;++$g)
    {
      $last_gen = true;
      $ancestor_data[$g] = Array();      
      $j=0;

      for($i=0;$i<pow(2,$g-1);++$i)
      {
	$child_line = ExtractSelfDataForID($lines,GetIDFromDataLine($ancestor_data[$g-1][$i]));

	if(GetFatherIDFromDataLine($child_line)!=0 || GetMotherIDFromDataLine($child_line)!=0) { $last_gen=false; }
	$ancestor_data[$g][$j] = ExtractSelfDataForID($lines,GetFatherIDFromDataLine($child_line));
	$ancestor_data[$g][$j+1] = ExtractSelfDataForID($lines,GetMotherIDFromDataLine($child_line));

	$j = $j+2;
      }
      if($last_gen) { break; }
    }
    return $ancestor_data;
  }

//--------------------------------------------------------------------------------------

  function ExtractDescendantDataForID($lines,$person_id,$max_depth)
  {
    $descendant_data = Array();
    $descendant_data[0] = Array();
    $descendant_data[0][0] = ExtractSelfDataForID($lines,$person_id);

    $num_lines = count($lines);
    for($g=1;$g<=$max_depth;++$g)
    {
      $num_in_prior_gen = count($descendant_data[$g-1]);
      $last_gen = true;
      $descendant_data[$g] = Array();      
      $j=0;

      for($p=0;$p<$num_in_prior_gen;++$p)
      {	
        $test_parent = GetIDFromDataLine($descendant_data[$g-1][$p]);
        for($i=0;$i<$num_lines;++$i)
        {
          if(GetFatherIDFromDataLine($lines[$i])==$test_parent || GetMotherIDFromDataLine($lines[$i])==$test_parent)
	  {
            $last_gen=false;
	    $descendant_data[$g][$j] = $lines[$i];
	    ++$j;
          }
	}
      }
      if($last_gen) { break; }
    }
    return $descendant_data;
  }

//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------

  function GetFullNameCodeString($master_fields,$index)
  {
    $line_count = count($master_fields);

    $name = "Not_Found";

    for($i=0;$i<$line_count;++$i)
    {
      if($master_fields[$i][0]==$index) { $name = $master_fields[$i][1];  break; }
    }
    return $name;
  }

//--------------------------------------------------------------------------------------

  function GetPersonIndexFromPersonID($master_fields,$person_id)
  {
    $line_count = count($master_fields);

    for($i=0;$i<$line_count;++$i)
    {
      if($master_fields[$i][0]==$person_id) { return $i; }
    }
    return -1;
  }

  function GetGenderFromPersonID($master_fields,$person_id)
  {
    $index = GetPersonIndexFromPersonID($master_fields,$person_id);
    $gender = $master_fields[$index][5];
    return $gender;
  }

  function GetFatherIndexFromPersonIndex($master_fields,$person_index)
  {
    if($person_index==-1) { return -1; }  
    return $master_fields[$person_index][11];
  }

  function GetMotherIndexFromPersonIndex($master_fields,$person_index)
  {
    if($person_index==-1) { return -1; }  
    return $master_fields[$person_index][12];
  }

  function GetChildrenArrayFromPersonID($master_fields,$person_id)
  {
    $children = Array();      
    $children[0] = Array();
    $children[1] = Array();
    $j=0;

    $gender = GetGenderFromPersonID($master_fields,$person_id);
    $line_count = count($master_fields);
    for($i=0;$i<$line_count;++$i)
    {
      if($person_id==$master_fields[$i][11] && $gender=="M") 
      {
        $children[0][$j] = $master_fields[$i][1]." [".$master_fields[$i][0]."]";
	$children[1][$j] = $master_fields[$i][12];
	++$j;
//        print "  $child_str - (with $other_parent_str)\n";
      }
      if($person_id==$master_fields[$i][12] && $gender=="F")
      {
        $children[0][$j] = $master_fields[$i][1]." [".$master_fields[$i][0]."]";
        $children[1][$j] = $master_fields[$i][11];
	++$j;
	//        print "  $child_str - (with $other_parent_str)\n";
      }  
    }
    return $children;
  }

//--------------------------------------------------------------------------------------

?>
