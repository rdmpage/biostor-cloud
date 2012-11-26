<?php
//  ISBN-ISSN.php  - ISBN/ISSN processing functions in PHP4.
//  Version 0.97
//  Robert D. Cameron, April 16, 2001.
//  Copyright (c) 2001,  GNU Public License, Version 2, applies.
//    Authoritative country/group code data needed.
//
//  This library includes functions for the following tasks.
//
//  (A) Checksum Calculation and Validation
//  (B) Hyphenation
//  (C) ISBN Country/Group look-up
//  (D) Error correction (single-digit errors)
//
//  General note: because of the similar processing logic,
//  generic ISN processing functions have been written to
//  handle both ISBN and ISSN.   These functions are instantiated
//  to ISBN with a length parameter of 10 and ISSN with a length
//  parameter of 8.   
//
//  -------------------------------------------------------------
//  ISN Checksum Calculation
//
//  ISBNs and ISSNs use similar modulus 11 checksum calculations.
//  For an ISBN, a weighted sum of the first nine digits is computed
//  with digit weights descending from 10 to 2.  Based on this
//  checksum, a check digit of 0 to 9 or X (representing 10) is 
//  determined as the value to be added to checksum to bring its
//  value to 0 modulus 11.   The calculation for ISSNs is similar,
//  except that the calculation is based on the weighted sum of the
//  first seven digits with weights descending from 8 to 2.
//
//  The mod11_checksum function provides for fault-tolerant
//  ISN checksum calculations starting with a given initial
//  weight.  Given the seven significant digits of an ISSN
//  or the nine significant digits of an ISBN, it calculates
//  the modulus 11 checksum that can be used for check digit
//  determination as above.  Given a complete ISN with check digit,
//  (that is, 8 digit ISSN or 10 digit ISBN) it confirms check
//  digit validity with a report of 0 as the modulus 11 checksum.
//  For applications involving ISN error correction, it also
//  computes a checksum for erroneous ISN strings, simply ignoring
//  extraneous or invalid characters.
//
function mod11_checksum ($protostring, $weight) {
  $checksum = 0;
  $pos = 0;
  while (($pos < strlen($protostring)) && ($weight >= 1)) {
    if (($protostring[$pos] >= '0') && ($protostring[$pos] <= '9')) {
      $checksum += $weight * $protostring[$pos];
      $weight--;
    }
    elseif (($weight == 1) && 
            (($protostring[$pos] == 'X') || ($protostring[$pos] == 'x'))) {
      $checksum += 10;
      $weight--;
    }
    $pos++;
  }
  return $checksum % 11;
}

// Check Digit Determinatoin
//
// Given a checksum for an incomplete ISN, determine the check
// digit that must be added.
//
function make_checkdigit($checksum) {
  $checkdigit = (11 - $checksum) % 11;
  return ($checkdigit == 10) ? "X" : (string) $checkdigit;
}

// ISN Validation
//
// Count bad ISN characters.  (ISSN or ISBN)
//
function bad_ISN_char_count ($proto_string) {
  $count = 0;
  for ($i = 0; $i < strlen($proto_string); $i++) {
    $c = $proto_string[$i];
    if ($c == '-') continue;
    if (($c < '0') || ($c > '9')) {$count++;}
  }
  if (($c == 'X') || ($c == 'x')) {
    return $count - 1;   // adjustment for a check digit
  }
  else {return $count;}
}

//
// ISN_clean: remove hyphens and transform check digit 'x' to 'X'. )
//
function ISN_clean ($isbn_proto) {
  return str_replace("x", "X", str_replace("-", "", $isbn_proto));
}

//
// ISN Checksum Validity
// An ISN is checksum-valid if, after removal of hyphens, (a) it is
// a complete ISN of the proper length, (b) consists only of the
// digits [0-9], except for the final check digit which may be X,
// and (c) has a weighted mod 11 checksum of 0.
//
function ISN_checksum_OK($isn_proto, $length) {
  $isn_proto = ISN_clean($isn_proto);
  return (strlen($isn_proto) == $length) &&
         (bad_ISN_char_count($isn_proto) == 0) &&
         (mod11_checksum($isn_proto, $length) == 0);
}

function ISBN_checksum_OK ($isbn_proto) {
  return ISN_checksum_OK($isbn_proto, 10);
}

function ISSN_checksum_OK ($issn_proto) {
  return ISN_checksum_OK($issn_proto, 8);
}

//
//  ISBN Hyphenation Conventions
//
//  ISBN hyphenation uses variable-length codes for county/group code,
//  publisher code and book code, with a total length of 9.  
//
//  The hyphenation rules can be described using codespace partition maps
//  structured as follows.  A code space partition map is an ordered list
//  of code prefixes that each establish the lower value of a range of 
//  codes having the same length.   For example, consider the ISBN
//  $country_group_partition following.

$country_group_partition = array('0', '80', '950', '9960', '99900');

//  This map specifies the following ranges for country/group prefixes:
//  0-7, 80-94, 950-995, 9960-9989, 99900-99999.  An ISBN beginning with
//  0 through 7 is thus hyphenated after the first character, while an
//  ISBN beginning with 950 through 995 is hyphenated after the third.

function prefix_length_from_map ($s, $map) {
  for ($i = 1; $i < sizeof($map); $i++) {
    if (strcmp($s, $map[$i]) < 0) return strlen($map[$i-1]);
  }
  return strlen($map[$i-1]);
}

function country_group_code ($isbn_proto) {
  global $country_group_partition;
  $isbn_proto = ISN_clean($isbn_proto);
  $cglen = prefix_length_from_map($isbn_proto, $country_group_partition);
  return substr($isbn_proto, 0, $cglen);
}

//
// For each country group, the remaining code space may similarly
// be described by a partition map.  This allows the length of
// the publisher code to be determined.  This in turn allows for
// the complete determination of proper ISBN hyphenation, since
// the check digit must be of length 1 and the book code is of
// length 10 - the lengths of the other elements.
//
// Note 1: this table uses a special convention for coding unassigned
// ranges with the space of a particular country-group code: the
// length of the "publisher code" is set to consume all the remaining
// digits of the ISBN except for the check digit.  This leaves
// no digits for a separate book code.  Hyphenation of such an
// "unassigned" ISBN will have only three hyphens, e.g., 
// 1-00000000-1, 93-0000000-4.
//
// Note 2: a method for keeping this table current is needed. 
//
// Note 3: the present table has been constructed using various
// sources on the web, including the ISBN.el module of Nelson
// Beebe (for Emacs Lisp) and the Business::ISBN.pm module of
// Brian D. Foy of Smith Renaud, Inc.  More work is needed.
//
// Note 4: Country names replaced by ISO3166 country codes,
// Apr. 16, 2001.   Multiple countries separated by ":" after
// a group description string.  INT used as the code for the
// international organizations group. 
//
$country_group_map =
  array(
    0 => array(array('00',200,7000,85000,900000,9500000), 
                     "English group 0:AU:CA:GI:IE:NZ:PR:ZA:SZ:GB:US:ZW"),
    1 => array(array('00000000',55000,869800,9999900), 
                     "English group 1:AU:CA:GI:IE:NZ:PR:ZA:SZ:GB:US:ZW"),
    2 => array(array('00',200,40000000,500,7000,84000,900000,9500000), 
                     "French group:FR:BE:CA:LU:CH"),
    3 => array(array('00',200,7000,85000,900000,9500000), 
                     "German group:AT:DE:CH"),
    4 => array(array('00',200,7000,85000,900000,9500000), "JP"),
    5 => array(array('00',200,7000,85000,900000,9500000), 
         "Former USSR group:RU:AM:AZ:BY:EE:GE:KZ:KG:LV:LT:MD:TJ:TM:UA:UZ"),
    7 => array(array('00',100,5000,80000,900000), "CN"),
    80 => array(array('00',200,7000,85000,900000), "Czech/Slovak:CZ:SK"),
    81 => array(array('00',200,7000,85000,900000), "IN"),
    82 => array(array('00',200,7000,90000,990000), "NO"),
    83 => array(array('00',200,7000,85000,900000), "PL"),
    84 => array(array('00',200,7000,85000,900000,95000,9700), "ES"),
    85 => array(array('00',200,7000,85000,900000), "BR"),
    86 => array(array('00',300,7000,80000,900000), "Balkans:YU:BA:HR:MK:SI"),
    87 => array(array('00',400,7000,85000,970000), "DK"),
    88 => array(array('00',200,7000,85000,900000), "Italian group:IT:CH"),
    89 => array(array('00',300,7000,85000,950000), "Korean group:KP:KR"),
    90 => array(array('00',200,5000,70000,800000,9000000), "Dutch group:NL:BE"),
    91 => array(array('0',20,500,6500000,7000,8000000,85000,9500000,970000), "SE"),
    92 => array(array('0',60,800,9000), "INT"), // International organizations
    93 => array(array('0000000'), "IN"),
    950 => array(array('00',500,9000,99000), "AR"),
    951 => array(array('0',20,550,8900,95000), "FI"),
    952 => array(array('00',200,5000,89,9500,99000), "FI"),
    953 => array(array('0',10,150,6000,96000), "HR"),
    954 => array(array('00',400,8000,90000), "BG"),
    955 => array(array('0',20,550,800000,9000,95000), "LK"),
    956 => array(array('00',200,7000), "CL"),
    957 => array(array('00',440,8500,97000), "TW"),
    958 => array(array('0',600,9000,95000), "CO"),
    959 => array(array('00',200,7000), "CU"),
    960 => array(array('00',200,7000,85000), "GR"),
    961 => array(array('00',200,6000,90000), "SI"),
    962 => array(array('00',200,7000,85000), "HK"),
    963 => array(array('00',200,7000,85000), "HU"),
    964 => array(array('00',300,5500,90000), "IR"),
    965 => array(array('00',200,7000,90000), "IL"),
    966 => array(array('00',500,7000,90000), "UA"),
    967 => array(array('0',60,900,9900,99900), "MY"),
    968 => array(array('000000',10,400,500000,6000,800,900000), "MX"),
    969 => array(array('0',20,400,8000), "PK"),
    970 => array(array('00',600,9000,91000), "MX"),
    971 => array(array('00',500,8500,91000), "PH"),
    972 => array(array('0',20,550,8000,95000), "PT"),
    973 => array(array('0',20,550,9000,95000), "RO"),
    974 => array(array('00',200,7000,85000,900000), "TH"),
    975 => array(array('00',300,6000,92000,980000), "TR"),
    976 => array(array('0',40,600,8000,95000), 
         "Caribbean Community:AG:BS:BB:BZ:KY:DM:GD:GY:JM:MS:KN:LC:VC:TT:VG"),
    977 => array(array('00',200,5000,70000), "EG"),
    978 => array(array('000',2000,30000), "NG"),
    979 => array(array('0',20,300000,400,700000,8000,95000), "ID"),
    980 => array(array('00',200,6000), "VE"),
    981 => array(array('00',200,3000), "SG"),
    982 => array(array('00',100,500000), 
                 "South Pacific:CK:FJ:KI:MH:NR:NU:SB:TK:TO:TV:VU:WS"),
    983 => array(array('000',2000,300000,50,800,9000,99000), "MY"),
    984 => array(array('00',400,8000,90000), "BD"),
    985 => array(array('00',400,6000,90000), "BY"),
    986 => array(array('000000'), "TW"),
    987 => array(array('00',500,9000,99000), "AR"),
    9952 => array(array('00000'), "AZ"),
    9953 => array(array('0',20,9000), "LB"),
    9954 => array(array('00',8000), "MA"),
    9955 => array(array('00',400), "LT"),
    9956 => array(array('00000'), "CM"),
    9957 => array(array('00',8000), "JO"),
    9958 => array(array('0',10,500,7000,9000), "BA"),
    9959 => array(array('00'), "Libya"),
    9960 => array(array('00',600,9000), "SA"),
    9961 => array(array('0',50,800,9500), "DZ"),
    9962 => array(array('00000'), "PA"),
    9963 => array(array('0',30,550,7500), "CY"),
    9964 => array(array('0',70,950), "GH"),
    9965 => array(array('00',400,9000), "KZ"),
    9966 => array(array('00',70000,800,9600), "KE"),
    9967 => array(array('00000'), "KG"),
    9968 => array(array('0',10,700,9700), "CR"),
    9970 => array(array('00',400,9000), "UG"),
    9971 => array(array('0',60,900,9900), "SG"),
    9972 => array(array('0',40,600,9000), "PE"),
    9973 => array(array('0',10,700,9700), "TN"),
    9974 => array(array('0',30,550,7500), "UY"),
    9975 => array(array('0',50,900,9500), "MD"),
    9976 => array(array('0',60,900,99000,9990), "TZ"),
    9977 => array(array('00',900,9900), "CR"),
    9978 => array(array('00',950,9900), "EC"),
    9979 => array(array('0',50,800,9000), "IS"),
    9980 => array(array('0',40,900,9900), "PG"),
    9981 => array(array('0',20,800,9500), "MA"),
    9982 => array(array('00',40000,800,9900), "ZM"),
    9983 => array(array('00',500,80,950,9900), "GM"),
    9984 => array(array('00',500,9000), "LV"),
    9985 => array(array('0',50,800,9000), "EE"),
    9986 => array(array('00',400,9000), "LT"),
    9987 => array(array('00',400,8800), "TZ"),
    9988 => array(array('0',30,550,7500), "GH"),
    9989 => array(array('0',30,600,9700), "MK"),
    99901 => array(array('00'), "BH"),   
    99903 => array(array('0',20,900), "MU"),
    99904 => array(array('0',60,900), "AN"),
    99905 => array(array('0',60,900), "BO"),
    99906 => array(array('0',60,900), "KW"),
    99908 => array(array('0',10,900), "MW"),
    99909 => array(array('0',40,950), "MT"),
    99910 => array(array('0000'), "SL"),
    99911 => array(array('00',600), "LS"),
    99912 => array(array('0',60,900), "BW"),
    99913 => array(array('0',30,600), "AD"),
    99914 => array(array('0',50,900), "SR"),
    99915 => array(array('0',50,800), "FK"),
    99916 => array(array('0',30,700), "NA"),
    99917 => array(array('0',30), "BN"),
    99918 => array(array('0',40,900), "FO"),
    99919 => array(array('0',40,900), "BJ"),
    99920 => array(array('0',50,900), "AD"),
    99921 => array(array('0',20,700), "QA"),
    99922 => array(array('0',50), "GT"),
    99923 => array(array('0',20,800), "SV"),
    99924 => array(array('0',30), "NI"),
    99925 => array(array('0',40,800), "PY"),
    99926 => array(array('0000',600), "HN"),
    99927 => array(array('0',30,600), "AL"),
    99928 => array(array('0',50,800), "GE"),
    99929 => array(array('0000'), "MN"),
    99930 => array(array('0',50,800), "AM"),
    99931 => array(array('0000'), "SC"),
    99932 => array(array('0',10), "MT"),
    99933 => array(array('00',300), "NP"),
    99934 => array(array('0'), "DO"),
    99935 => array(array('0000'), "HT"),
    99936 => array(array('0000'), "BT"),
    99937 => array(array('0',20), "MO")
  );

function country_group_name ($isbn_proto) {
  global $country_group_map;
  $cg = country_group_code($isbn_proto);
  if (is_array($country_group_map[$cg])) return $country_group_map[$cg][1];
  else return "unassigned country/group";
}

//
//  Determine whether an ISBN is within an assigned range of publisher 
//  codes for its country/group.
//
function is_assigned ($isbn_proto) {
  $isbn_proto = ISN_clean($isbn_proto);
  global $country_group_map;
  $cg = country_group_code($isbn_proto);
  $cglen = strlen($cg);
  $after_cg = substr($isbn_proto, $cglen);
  if (is_array($country_group_map[$cg])) {
    $publen = prefix_length_from_map($after_cg, $country_group_map[$cg][0]);
    return ($cglen + $publen < 9);
  }
  else return false;
}

//
// Generate the canonical hyphenated form of an ISBN.
// (Checksum-valid ISBN assumed as input).
//
function canonical_ISBN ($isbn_proto) {
  $isbn_proto = ISN_clean($isbn_proto);
  global $country_group_map;
  $cg = country_group_code($isbn_proto);
  $cglen = strlen($cg);
  $pubandbook = substr($isbn_proto, $cglen, 9-$cglen);
  $checkdigit = $isbn_proto[9];
  if (is_array($country_group_map[$cg])) {
    $publen = prefix_length_from_map($pubandbook, $country_group_map[$cg][0]);
    if ($cglen + $publen == 9) return "$cg-$pubandbook-$checkdigit";
    else {
      $pubcode = substr($pubandbook, 0, $publen);
      $bookno = substr($pubandbook, $publen);
      return "$cg-$pubcode-$bookno-$checkdigit";
    }
  }
  else return "$cg-$pubandbook-$checkdigit";
}


//
// Generate the canonical hyphenated form of an ISSN.
// (Checksum-valid ISSN assumed as input).
//
function canonical_ISSN ($issn_proto) {
  $issn_proto = ISN_clean($issn_proto);
  return substr($issn_proto, 0, 4) . "-" . substr($issn_proto, 4, 4);
}

//
//  Length-based canonical ISN function for use within generic ISN
//  error correction function.
//
function canonical_ISN ($isn_proto, $length) {
  if ($length == 8) return canonical_ISSN($isn_proto);
  else return canonical_ISBN($isn_proto);
}

//
//  ISN classification.
//
//  Given a dehyphenated ISN protostring and an expected length
//  (ISSN: 8, ISBN: 10), analyze it and return one
//  of the following classification strings:
//    "checksumOK" - a proper length ISN with valid check digit
//    "single_error" - an ISN with a single digit error.
//    "short" - a string that is too short, but has only valid ISN chars.
//    "short/invalid" - a string that is too short and has invalid chars.
//    "invalid" - a string with more than 2 errors.
//    "long" - a string that is too long, but has only valid ISN chars.
//    "long/invalid" - a string that is too long and has invalid chars.
//
function ISN_classifier($clean, $length) {
  $bad_chars = bad_ISN_char_count($clean);
  if (strlen($clean) < $length-1)
    return ($bad_chars == 0) ? "short" : "short/invalid";
  elseif (strlen($clean) == $length-1)
    return ($bad_chars == 0) ? "single_error" : "short/invalid";
  elseif (strlen($clean) == $length) {
    if ($bad_chars == 0)
      if (mod11_checksum($clean, $length) == 0) return "checksumOK";
      else return "single_error";
    else
      return ($bad_chars == 1) ? "single_error" : "invalid";
  }
  elseif (strlen($clean) == $length+1) {
    if ($bad_chars == 0) return "single_error";
    elseif (($bad_chars == 1) && (mod11_checksum($clean, $length) == 0)) 
      return "single_error";
    else return "long/invalid";
  }
  else return ($bad_chars == 0) ? "long" : "long/invalid";
}

function ISSN_classifier($clean) {
  return ISN_classifier($clean, 8);
}

//
//  ISBN classification has an additional category "bookland" - an apparent 
//  Bookland encoded EAN.
//
function ISBN_classifier($clean) {
  if ((strlen($clean) == 13) && (substr($clean, 0, 3) == '978')
      && (bad_ISN_char_count($clean) == 0))
    return "bookland";
  else return ISN_classifier($clean, 10);
}

//
// ISN Error Correction
//
// Given an erroneous ISN protostring, generate possible correct ISN strings
// of the given $length (8 => ISSN, 10 => ISBN) with single character corrections.  
//  - For protostrings with $length-1 significant digits, try adding a 
//    correct character at each possible position.
//  - For protostrings with $length digits, generate all possible single character
//    replacements and pairwise interchanges that yield a valid ISN.
//  - For protostrings of $length+1 digits, generate all valid ISNs formed 
//    by deletion of a single character.

function generate_ISN_corrections($ISN_proto, $length) {
  $corrections = array();
  $ISN_proto = ISN_clean($ISN_proto);
  if (strlen($ISN_proto) == $length-1) {
    // First try possible insertions in the first $length-1 positions.
    for ($pos = 0; $pos < $length-1; $pos++) {
      for ($i = 0; $i <= 9; $i++) {
        $ISN_new = substr($ISN_proto, 0, $pos) . (string) $i . 
                    substr($ISN_proto, $pos);
        if (ISN_checksum_OK($ISN_new, $length)) {
          $corrections[canonical_ISN($ISN_new, $length)] = true;
        }
      }
    }
    if ($ISN_proto[$length-2] != 'X') {
      $ISN_new = $ISN_proto .  
                  make_checkdigit(mod11_checksum($ISN_proto, $length));
      $corrections[canonical_ISN($ISN_new, $length)] = true;
    }
  }
  elseif (strlen($ISN_proto) == $length) {
    // Try replacements and interchanges for first nine positions.
    for ($pos = 0; $pos < $length-1; $pos++) {
      $current = $ISN_proto[$pos];
      // try all possible replacements
      for ($i = 0; $i <= 9; $i++) {
        $ISN_proto[$pos] = (string) $i;
        if (ISN_checksum_OK($ISN_proto, $length)) {
          $corrections[canonical_ISN($ISN_proto, $length)] = true;
        }
      }
      // try the next character interchange (unless there is a final "X").
      $ISN_proto[$pos] = $ISN_proto[$pos+1];
      $ISN_proto[$pos+1] = $current;
      if (($ISN_proto[$pos] != 'X') && ISN_checksum_OK($ISN_proto, $length)) {
        $corrections[canonical_ISN($ISN_proto, $length)] = true;
      }
      // reset to the original values before moving on to the next position.
      $ISN_proto[$pos+1] = $ISN_proto[$pos];
      $ISN_proto[$pos] = $current;
    }
    // Now replace the check digit with the correct value.
    $ISN_proto[$length-1] = 
      make_checkdigit(mod11_checksum(substr($ISN_proto, 0, $length-1), $length));
    if (bad_ISN_char_count($ISN_proto) == 0) {
      $corrections[canonical_ISN($ISN_proto, $length)] = true;
    }
  }
  elseif (strlen($ISN_proto) == $length+1) {
    for ($pos = 0; $pos <= $length; $pos++) {
      $ISN_new = substr($ISN_proto, 0, $pos) . substr($ISN_proto, $pos+1);
      if (ISN_checksum_OK($ISN_new, $length)) {
        $corrections[canonical_ISN($ISN_new, $length)] = true;
      }
    }
  }
  return $corrections;
}


function generate_ISBN_corrections($isbn_proto) {
  return generate_ISN_corrections($isbn_proto, 10);
}

function generate_ISSN_corrections($issn_proto) {
  return generate_ISN_corrections($issn_proto, 8);
}
?>