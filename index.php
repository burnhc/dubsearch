<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Encode+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="stylehome.css">
	<title>DubSearch</title>
</head>

<body>
	<div class="container-fluid">
		<div class="banner">
		<div class="uw-patch">University of Washington</div>
			<div class="wordmark"><a href="http://searchrec.ischool.uw.edu/arya3/INFO498/search.php">UNIVERSITY OF WASHINGTON</a></div>
		</div>
	</div>
	<main>
		<div class="mainBody">
			<div class="explore">DubSearch</div>
			<p>Explore University of Washington documents in this collection by entering a query below.</p>
			<form id="queryForm" action="search.php" method="post">
				<input class="query" id="queryInput" type="text" size="40" name="search_string"
					placeholder="Try searching: ischool, pandemic, news, chirag shah" value="">
				<div class="buttons">
					<input class="submit-button" type="submit" value="SEARCH">
					<input class="submit-button" type="button" onclick="randomSearch()" value="I'M FEELING BOUNDLESS">
				</div>
			</form>
			</div>

		</div>
		<div class="footer"> 
			<a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">
			<img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png">
			</a>
			<br>This work is licensed under a 
			<a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">
				Creative Commons Attribution-NonCommercial 4.0 International License.</a>	
		</div>
	</main>


<script>
function randomSearch() {
	const queries = ['ischool', 'pandemic', 'news',
			 'technology', 'alumni', 'husky',
			 'policy', 'research', 'seattle',
			 'engineering', 'boundless', 'chirag shah'];
	const randomQuery = queries[Math.floor(Math.random() * queries.length)];
	const input = document.getElementById("queryInput");
	input.value = randomQuery;
	document.getElementById("queryForm").submit();
}	
	
</script>

</body>

</html>
