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

$query = "SELECT * FROM `protein_alias` WHERE alias = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene);
$stmt->execute();
$stmt->bind_result($taxon_id, $protein_id, $alias, $source);
?>

<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation - View Protein Alias</title>
<?php require_once("includes/menu.php"); ?>
    <h1>View Protein Alias</h1>
	<form action="protein_alias_view.php" method="get" role="form">
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
        <th>Taxon ID</th>
        <th>Protein ID</th>
        <th>Alias</th>
        <th>Source</th>
      </tr>
<?php
while($stmt->fetch())
{
	echo "
      <tr>
        <td>$taxon_id</td>
        <td>$protein_id</td>
        <td>$alias</td>
        <td>$source</td>
      </tr>
	";
}

$stmt->close();
$con->close();
?>
    </table>

<?php require_once("includes/footer.php"); ?>