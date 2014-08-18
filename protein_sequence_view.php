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

$query = "SELECT * FROM `protein_sequence` WHERE protein_id = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene);
$stmt->execute();
$stmt->bind_result($protein_id, $sequence);
?>

<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation - View Protein Sequence</title>
<?php require_once("includes/menu.php"); ?>
    <h1>View Protein Sequence</h1>
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
        <th>Protein ID</th>
        <th>Sequence</th>
      </tr>
<?php
while($stmt->fetch())
{
	echo "
      <tr>
        <td>$protein_id</td>
        <td>$sequence</td>
      </tr>
	";
}

$stmt->close();
$con->close();
?>
    </table>

<?php require_once("includes/footer.php"); ?>