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

$query = "SELECT * FROM `protein_actions` WHERE protein_a = ?;";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $gene);
$stmt->execute();
$stmt->bind_result($protein_a, $protein_b, $mode, $action, $a_is_acting, $score, $sources, $transferred_sources);
?>

<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation - View Protein Actions</title>
<?php require_once("includes/menu.php"); ?>
    <h1>View Protein Actions</h1>
	<form action="protein_actions_view.php" method="get" role="form">
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
        <th>Mode</th>
        <th>Action</th>
		<th>A is Acting</th>
		<th>Score</th>
		<th>Sources</th>
		<th>Transferred Sources</th>
      </tr>
<?php
while($stmt->fetch())
{
	
	echo "
      <tr>
        <td>$protein_a</td>
        <td>$protein_b</td>
        <td>$mode</td>
        <td>$action</td>
		<td>$a_is_acting</td>
		<td>$score</td>
		<td>$sources</td>
		<td>$transferred_sources</td>
      </tr>
	";
}

$stmt->close();
$con->close();
?>
    </table>

<?php require_once("includes/footer.php"); ?>