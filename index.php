<?php require_once("includes/header.php"); ?>
  <title>Degrees of Gene Separation</title>
<?php require_once("includes/menu.php"); ?>
    <h1>Degrees of Gene Separation</h1>
    <p>Determine how many degrees of separation exist between your genes.</p>
	<form action="protein_link_do.php" method="get" role="form">
      <div class="form-group">
        <label for="gene1">Gene 1: </label>
        <input id="gene1" class="form-control" type="text" name="gene1" />
      </div>
      <div class="form-group">
        <label for="gene2">Gene 2: </label>
        <input id="gene2" class="form-control" type="text" name="gene2" />
      </div>
	  <div class="form-group">
        <label for="score_threshold">Score (0 - 1): </label>
        <select name="score_threshold">
          <option value="0.8">Strong Interaction</option>
          <option value="0.6">Medium Interaction</option>
          <option value="0.4">Low Interaction</option>
		  <option value="0">All Interactions</option>
        </select>
      </div>
      <div class="form-group">
        <input class="bt btn-danger" type="submit" value="Submit" />
      </div>
	</form>
<?php require_once("includes/footer.php"); ?>