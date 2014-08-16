<?php
require_once("includes/db.php");
// Create connection
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "SELECT * FROM `protein_alias` WHERE protein_id = 'ENSP00000397982';";
$stmt = $con->prepare($query);
$stmt->execute();
$stmt->bind_result($taxon_id, $protein_id, $alias, $source);
?>

<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation - View Protein Alias</title>
<?php require_once("includes/menu.php"); ?>
    <h1>View Protein Alias</h1>
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