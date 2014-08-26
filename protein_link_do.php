<?php
/* Acquiring values from HTTP header sent from roster_modify.php */
$gene1 = $_GET['gene1'];
$gene2 = $_GET['gene2'];
$test = "ENSP00000417281";

require_once("includes/db.php");
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "SELECT protein_b FROM `protein_links` WHERE protein_a = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene1);
$stmt->execute();
$stmt->bind_result($gene1_protein_b);
$gene1_level1 = array();
while($stmt->fetch())
{
	array_push($gene1_level1, $gene1_protein_b);
}
$stmt->close();

$query = "SELECT protein_b FROM `protein_links` WHERE protein_a = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene2);
$stmt->execute();
$stmt->bind_result($gene2_protein_b);
$gene2_level1 = array();
while($stmt->fetch())
{
	array_push($gene2_level1, $gene2_protein_b);
}
$stmt->close();

$temp = explode (".", $gene1);
$gene1_id = $temp[1];
$temp = explode (".", $gene2);
$gene2_id = $temp[1];

$gene1_level1_length = count($gene1_level1);
$gene2_level1_length = count($gene2_level1);
$match_id = array();
for ($i = 0; $i < $gene1_level1_length; $i++)
{
	if ($gene1_level1[$i] == $gene2)
	{
		$level0_match = true;
	}
	if (!$level0_match)
	{
		for ($j = 0; $j < $gene2_level1_length; $j++)
		{
			if ($gene1_level1[$i] == $gene2_level1[$j]) 
			{
				$temp = explode (".", $gene1_level1[$i]);
				array_push($match_id, $temp[1]);
				$level1_match = true;
			}
		}
	}
}
?>
<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation</title>
<?php require_once("includes/menu.php"); ?>
    <h1>Degrees of Gene Separation</h1>
<?php
if ($level0_match)
{
	echo "<h2>Level 0 Matches</h2>";
	echo "$gene1 -> $gene2</br>";
	echo "$gene1_id -> $gene2_id</br>";
}
else if (!$level0_match && $level1_match)
{
	echo "<h2>Level 1 Matches</h2>";
	$newarray = "'" . implode("', '", $match_id) . "'";
	$query = "SELECT protein_id, alias FROM `protein_alias` WHERE protein_id IN ($newarray) AND source = 'BLAST_UniProt_DE';";
	$stmt = $con->prepare($query);
	$stmt->execute();
	$stmt->bind_result($protein_id, $alias);
	while($stmt->fetch())
	{
		echo "$gene1_id -> $protein_id = $alias -> $gene2_id</br>";
	}
	$stmt->close();
}
?>
<?php require_once("includes/footer.php"); ?>