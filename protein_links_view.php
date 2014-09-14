<?php
require_once("includes/db.php");

$gene =$_GET['gene'];
if (empty($gene)){
	$gene = '';
}

// Create connection
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "SELECT * FROM `protein_links` WHERE protein_a = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene);
$stmt->execute();
$stmt->bind_result($protein_a, $protein_b, $neighborhood, $fusion, $cooccurence, $coexpression, $experimental, $database_results, $textmining, $combined_score);
?>

<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation - View Protein Links</title>
<?php require_once("includes/menu.php"); ?>
    <h1>View Protein Links</h1>
	<form action="protein_links_view.php" method="get" role="form">
      <div class="form-group">
        <label for="gene">Gene: </label>
        <input id="gene" class="form-control" type="text" name="gene" />
      </div>
      <div class="form-group">
        <input class="bt btn-danger" type="submit" value="Submit" />
      </div>
	</form>
    <table class="table table-striped">
      <tr>
        <th>Protein A</th>
        <th>Protein B</th>
        <th>Neighborhood</th>
        <th>Fusion</th>
		<th>Cooccurence</th>
		<th>Coexpression</th>
		<th>Experimental</th>
		<th>Database Results</th>
		<th>Textmining</th>
		<th>Combined Score</th>
      </tr>
<?php
while($stmt->fetch())
{
	echo "
      <tr>
        <td>$protein_a</td>
        <td>$protein_b</td>
        <td>$neighborhood</td>
        <td>$fusion</td>
		<td>$cooccurence</td>
		<td>$coexpression</td>
		<td>$experimental</td>
		<td>$database_results</td>
		<td>$textmining</td>
		<td>$combined_score</td>
      </tr>
	";
}

$stmt->close();
$con->close();
?>
    </table>

<?php require_once("includes/footer.php"); ?>