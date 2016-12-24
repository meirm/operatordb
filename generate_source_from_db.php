<?php

/* connect to the DB */

include_once("db.php");

function execute_sql($handler,$sql,$xdebug=0)
{
	if($xdebug==1)
	{
		printf("SQL: %s\n",$sql);
	}
	$result = $handler->query($sql);
	if($result==false)
	{
		echo "SQL Error #1: ".$handler->error."\n";
		exit;
	}
	if($xdebug==1)
	{
		printf("\n");
	}
	return $result;
}

function get_rows_sql($handler,$sql,$xdebug=0)
{
	$arr = array();
	$r = execute_sql($handler,$sql,$xdebug);
	for($i=0;$i<$r->num_rows;$i++)
	{
		$arr[$i] = $r->fetch_array(MYSQLI_ASSOC);
	}
	return $arr;
}

function add_node(&$root,$prefix,$digit,$country,$organisation,$network,$abbreviated_name,$mcc,$mnc,$sim,$last_update)
{
	if(strlen($digit)> 0)
	{
		$current_digit = substr($digit,0,1);
		$remaining_digits = substr($digit,1);

		if(!isset($root[$current_digit]))
		{
			$prefix = $prefix . $current_digit;
			$root[$current_digit] = array('prefix'=>$prefix);
		}
		$root = &$root[$current_digit];
		add_node($root,$prefix,$remaining_digits,$country,$organisation,$network,$abbreviated_name,$mcc,$mnc,$sim,$last_update);
	}
	else
	{
		$root['country']=$country;
		$root['organisation']=$organisation;
		$root['network']=$network;
		$root['abbreviated_name']=$abbreviated_name;
		$root['mcc']=$mcc;
		$root['mnc']=$mnc;
		$root['sim']=$sim;
		$root['last_update']=$last_update;
		$root['operator_code']=$mcc.".".$mnc;
	}
}

function print_node($root,$ident,$tab,$index,$format)
{
	$has_sub = 0;
	for($i=0;$i<10;$i++)
	{
		if(isset($root[$i]))
		{
			$has_sub = 1;
			break;
		}
	}
	if($has_sub==1)
	{
		if($format=="c")
		{
			echo $ident."switch(imsi[".$index."])\n";
		}
		else if($format=="php")
		{
			echo $ident."switch(\$imsi[".$index."])\n";
		}
		echo $ident."{\n";
		
		for($i=0;$i<10;$i++)
		{
			if(isset($root[$i]))
			{
				echo $ident.$tab."case '".$i."':\n";
				echo $ident.$tab."{\n";
				print_node($root[$i],$ident.$tab.$tab,$tab,$index+1,$format);
				echo $ident.$tab.$tab."break;\n";
				echo $ident.$tab."}\n";
			}
		}
		echo $ident."}\n";
	}
	else
	{
		if($format=="c")
		{
			$varprefix = "*";
		}
		else if($format=="php")
		{
			$varprefix = "$";
		}
		
		if(isset($root['country']))
		{
			echo $ident.$varprefix."country = \"".$root['country']."\";\n";
		}
		if(isset($root['organisation']))
		{
			echo $ident.$varprefix."organisation = \"".$root['organisation']."\";\n";
		}
		if(isset($root['network']))
		{
			echo $ident.$varprefix."network = \"".$root['network']."\";\n";
		}
		if(isset($root['abbreviated_name']))
		{
			echo $ident.$varprefix."abbreviated_name = \"".$root['abbreviated_name']."\";\n";
		}
		if(isset($root['mcc']))
		{
			echo $ident.$varprefix."mcc = \"".$root['mcc']."\";\n";
		}
		if(isset($root['mnc']))
		{
			echo $ident.$varprefix."mnc = \"".$root['mnc']."\";\n";
		}
		if(isset($root['sim']))
		{
			echo $ident.$varprefix."sim = \"".$root['sim']."\";\n";
		}
		if(isset($root['last_update']))
		{
			echo $ident.$varprefix."last_update = \"".$root['last_update']."\";\n";
		}
		if(isset($root['operator_code']))
		{
			echo $ident.$varprefix."operator_code = \"".$root['operator_code']."\";\n";
		}
	}
}

$root = array('prefix'=>'');
$op_recs = get_rows_sql($mysqli1,"select * from ts25");

$n = sizeof($op_recs);
for($i=0;$i<$n;$i++)
{
	$r = $op_recs{$i};
	$country = $r['cc3'];
	$organisation = $r['organisation'];
	$network = $r['network'];
	$abbreviated_name = $r['abbreviated_name'];
	$mcc = $r['mcc'];
	$mnc = $r['mnc'];
	$sim = $r['sim'];
	$last_update = $r['last_update'];

	$prefix = $mcc . $mnc;
	add_node($root,"",$prefix,$country,$organisation,$network,$abbreviated_name,$mcc,$mnc,$sim,$last_update);
}

$output_format = "c";
if($argc>1)
{
	$output_format = $argv[1];
}

if($output_format=="c")
{
	echo "//\n";
	echo "//  operatordb.c\n";
	echo "//  operatordb\n";
	echo "//\n";
	echo "//  Created by ".get_current_user()." on ".gmdate('Y-m-d H:i:s e').".\n";
	echo "//\n";
	echo "\n";
	echo "\n";
	echo "void get_operator_from_imsi(const char *imsi,\n";
	echo "                            char **country,\n";
	echo "                            char **organisation,\n"; 
	echo "                            char **network,\n"; 
	echo "                            char **abbreviated_name,\n";
	echo "                            char **mcc,\n";
	echo "                            char **mnc,\n";
	echo "                            char **sim,\n";
	echo "                            char **last_update,\n";
	echo "                            char **operator_code)\n";
	echo "{\n";
	echo "    *country = \"\";\n";
	echo "    *organisation = \"\";\n";
	echo "    *network = \"\";\n";
	echo "    *abbreviated_name = \"\";\n";
	echo "    *mcc = \"\";\n";
	echo "    *mnc = \"\";\n";
	echo "    *sim = \"\";\n";
	echo "    *last_update = \"\";\n";
	echo "    *operator_code = \"\";\n";
	echo "\n";

	$tab = "    ";
	$ident = $tab;
	print_node($root,$ident,$tab,0,"c");
	echo "}\n";
}
else if ($output_format=="php")
{
	echo "<?php\n";
	echo "//  operatordb.php\n";
	echo "//  operatordb\n";
	echo "//\n";
	echo "//  Created by ".get_current_user()." on ".gmdate('Y-m-d H:i:s e').".\n";
	echo "//\n";
	echo "\n";
	echo "\n";
	echo "function get_operator_from_imsi(\$imsi)\n";
	echo "{\n";
	echo "    \$country = \"\";\n";
	echo "    \$organisation = \"\";\n";
	echo "    \$network = \"\";\n";
	echo "    \$abbreviated_name = \"\";\n";
	echo "    \$mcc = \"\";\n";
	echo "    \$mnc = \"\";\n";
	echo "    \$sim = \"\";\n";
	echo "    \$last_update = \"\";\n";
	echo "    \$operator_code = \"\";\n";
	echo "\n";

	$tab = "    ";
	$ident = $tab;
	print_node($root,$ident,$tab,0,"php");
	echo "    \$a = array();\n";
	echo "    \$a['country']= \$country;\n";
	echo "    \$a['organisation']= \$organisation;\n";
	echo "    \$a['network']= \$network;\n";
	echo "    \$a['abbreviated_name']= \$abbreviated_name;\n";
	echo "    \$a['mcc']= \$mcc;\n";
	echo "    \$a['mnc']= \$mnc;\n";
	echo "    \$a['sim']= \$sim;\n";
	echo "    \$a['last_update']= \$last_update;\n";
	echo "    \$a['operator_code']= \$operator_code;\n";
	echo "    return \$a;\n";
	echo "}\n";
}
else
{
	fprintf(STDERR,"Unknown output format '$output_format'. Choose 'c' or 'php'\n");
}
