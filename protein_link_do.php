<?php
/* Acquiring values from HTTP header sent from roster_modify.php */
$gene1 = $_GET['gene1'];
$gene2 = $_GET['gene2'];
$score_threshold = $_GET['score_threshold'];

require_once("includes/db.php");
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Acquire taxon id and protein id for gene 1
$query = "SELECT taxon_id, protein_id FROM `protein_alias` WHERE alias = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene1);
$stmt->execute();
$stmt->bind_result($taxon_id, $protein_id);
$gene1_id = array();
while($stmt->fetch())
{
	$temp = $taxon_id . "." . $protein_id;
	array_push($gene1_id, $temp);
}
$stmt->close();

// Acquire taxon id and protein id for gene 2
$query = "SELECT taxon_id, protein_id FROM `protein_alias` WHERE alias = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene2);
$stmt->execute();
$stmt->bind_result($taxon_id, $protein_id);
$gene2_id = array();
while($stmt->fetch())
{
	$temp = $taxon_id . "." . $protein_id;
	array_push($gene2_id, $temp);
}
$stmt->close();

// Create queriable array of gene 1 and gene 2 ids
$gene1_id_array = "'" . implode("', '", $gene1_id) . "'";
$gene2_id_array = "'" . implode("', '", $gene2_id) . "'";

// Acquire interacting protein and score of gene 1
$query = "SELECT protein_b, combined_score FROM `protein_links` WHERE protein_a IN ($gene1_id_array);";
$stmt = $con->prepare($query);
$stmt->execute();
$stmt->bind_result($gene1_protein_b, $gene1_combined_score);
$gene1_level1_id = array();
$gene1_level1_score = array();
while($stmt->fetch())
{
	array_push($gene1_level1_id, $gene1_protein_b);
	$gene1_level1_score[$gene1_protein_b] = $gene1_combined_score;
}
$stmt->close();

// Acquire interacting protein and score of gene 2
$query = "SELECT protein_b, combined_score FROM `protein_links` WHERE protein_a IN ($gene2_id_array);";
$stmt = $con->prepare($query);
$stmt->execute();
$stmt->bind_result($gene2_protein_b, $gene2_combined_score);
$gene2_level1_id = array();
$gene2_level1_score = array();
while($stmt->fetch())
{
	array_push($gene2_level1_id, $gene2_protein_b);
	$gene2_level1_score[$gene2_protein_b]= $gene2_combined_score;
}
$stmt->close();

// Check for matches
$gene2_id_length = count($gene2_id);
$gene1_level1_id_length = count($gene1_level1_id);
$gene2_level1_id_length = count($gene2_level1_id);
$match_id = array();
$level1_score = array();
// Level 0 match
$interaction_score = 1;
for ($i = 0; $i < $gene1_level1_id_length; $i++)
{
	for ($j = 0; $j < $gene2_id_length; $j++) 
	{
		if ($gene1_level1_id[$i] == $gene2_id[$j])
		{
			$level0_match = true;
			$interaction_score = $interaction_score * ($gene1_level1_score["$gene2_id[$j]"] / 1000);
		}
	}
}
// Level 1 match if Level 0 match does not exist or is below the score threshold
if (!$level0_match || $interaction_score < $score_threshold)
{
	for ($i = 0; $i < $gene1_level1_id_length; $i++)
	{
		for ($j = 0; $j < $gene2_level1_id_length; $j++)
		{
			if ($gene1_level1_id[$i] == $gene2_level1_id[$j]) 
			{
				$temp = explode (".", $gene1_level1_id[$i]);
				array_push($match_id, $temp[1]);
				$level1_score[$temp[1]] = ($gene1_level1_score["$gene1_level1_id[$i]"] / 1000) * ($gene2_level1_score["$gene2_level1_id[$j]"] / 1000);
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
// Print Level 0 match with score
echo "<h2>Level 0 Matches</h2>";
if ($level0_match && $interaction_score >= $score_threshold)
{
	echo "$gene1 -> $gene2 score = $interaction_score</br>";
}
else 
{
	echo "No Matches.";
}
// Print Level 1 matches with score
if (!$level0_match || $interaction_score < $score_threshold)
{
	echo "<h2>Level 1 Matches</h2>";
	$match_id_array = "'" . implode("', '", $match_id) . "'";
	$query = "SELECT protein_id, alias FROM `protein_alias` WHERE protein_id IN ($match_id_array) AND (source = 'BLAST_UniProt_DE' OR source = 'BLAST_KEGG_NAME' or source = 'Ensembl_HGNC_Approved_Name' or source = 'Ensembl_HGNC_Name_Aliases' or source = 'BLAST_UniProt_DE Ensembl_UniProt_DE' or source = 'BLAST_KEGG_NAME Ensembl_EntrezGene_synonym Ensembl_HGNC_Aliases Ensembl_HGNC_synonym' or source = 'BLAST_UniProt_ID Ensembl_HGNC_UniProt_ID_(mapped_data_supplied_by_UniProt)_ID Ensembl_UniProt Ensembl_UniProt_ID' or source = 'Ensembl_UniProt_GN' or source ='Ensembl_HGNC_curated_gene_synonym' or source = 'Ensembl_UniProt_DE');";
	$stmt = $con->prepare($query);
	$stmt->execute();
	$stmt->bind_result($protein_id, $alias);
	$temp_array = array();
	while($stmt->fetch())
	{
		if ($level1_score[$protein_id] >= $score_threshold)
		{
			echo "$gene1 -> $protein_id = $alias -> $gene2 $source score = $level1_score[$protein_id]</br>";
		}		
	}
	$stmt->close();
}
else if ($level0_match && !$level1_match)
{
	echo "<h2>Level 1 Matches</h2>";
	echo "No Matches.";
}
?>
<?php require_once("includes/footer.php"); ?>