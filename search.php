<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link
		href="https://fonts.googleapis.com/css2?family=Encode+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap"
		rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&amp;display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
	<title>DubSearch</title>
</head>

<body>
	<div class="container-fluid">
		<div class="banner">
		<div class="uw-patch">University of Washington</div>
			<div class="wordmark"><a href="http://searchrec.ischool.uw.edu/arya3/INFO498">UNIVERSITY OF WASHINGTON</a></div>
		</div>
	</div>
	<main>
		<div class="explore">DubSearch</div>
		<form id="query-form" action="search.php" method="post">
			<input id="query" class="query" type="text" size=40 name="search_string" placeholder="Search..." value=""/>
			<input class="submit-button" type="submit" value="SEARCH" />
		</form>

<script>
function searchFromSuggestion(query) {
	const input = document.getElementById("query");
	input.value = query;
	document.getElementById("query-form").submit();
};
</script>


<div class="results">
<?php

if (isset($_POST["search_string"]))
{
   $search_string = trim($_POST["search_string"]);
   $qfile = fopen("query.py", "w");

   echo "<p><span class=\"searchterm\">You searched for: </span><span class=\"keyword\" onclick=\"searchFromSuggestion('$search_string')\">\"$search_string\"</span></p>\n";
	 
   $log_file = fopen("log.txt", "a");
   date_default_timezone_set('America/Los_Angeles');
   $log_entry = $_SERVER["REMOTE_ADDR"] . "," . $search_string . "," . date('m/d/Y h:i:s a', time()) . "\n";

   fwrite($log_file, $log_entry);
   fclose($log_file);

   fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
   fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
   fwrite($qfile, "index = pt.IndexFactory.of(\"./uw_index/data.properties\")\n");
   fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n");

   for ($i=0; $i<10; $i++)
   {
      fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",tf_idf.transform(queries).docid[$i]))\n");
      fwrite($qfile, "print(index.getMetaIndex().getItem(\"title\",tf_idf.transform(queries).docid[$i]))\n");
   }
   
   fclose($qfile);

   $rec_file = fopen("recommendations.py", "w");
   fwrite($rec_file, "import pandas as pd\n");
   fwrite($rec_file, "log = pd.read_csv(\"log.txt\", header=None, names=[\"ip\", \"query\", \"datetime\"])\n");
   fwrite($rec_file, "queries_by_user = log.groupby([\"ip\"])[\"query\"]\n");
   fwrite($rec_file, "user_query = \"$search_string\"\n");
   fwrite($rec_file, "similar_queries = []\n");
   fwrite($rec_file, "for ip, queries in queries_by_user:\n");
   fwrite($rec_file, "\tqueries = queries.unique()\n");
   fwrite($rec_file, "\tif user_query in queries:\n");
   fwrite($rec_file, "\t\tfor query in queries:\n");
   fwrite($rec_file, "\t\t\tif query != user_query:\n");
   fwrite($rec_file, "\t\t\t\tsimilar_queries.append(query)\n");
   fwrite($rec_file, "similar_queries = pd.Series(similar_queries)\n");
   fwrite($rec_file, "similar_queries = similar_queries.value_counts().index.tolist()\n");
   fwrite($rec_file, "for query in similar_queries[0:3]:\n\tprint(query)\n");

   fclose($rec_file);

   exec("ls | nc -u 127.0.0.1 10010");
   sleep(3);

   shell_exec("/usr/bin/python3.6 recommendations.py > recs");
   sleep(3);

   $stream = fopen("recs", "r");
   echo "<p><span class=\"searchterm\">People also searched for: </span>";
   while(($line=fgets($stream))!=false) 
   {
      $term = trim($line);
      echo "<span class=\"keyword\" onclick=\"searchFromSuggestion('$term')\">\"$term\"  </span>";      
   }
   echo "</p>\n";
   fclose($stream);

   $stream = fopen("output", "r");

   $line=fgets($stream);

   while(($line=fgets($stream))!=false) 
   {
      $clean_line = preg_replace('/\s+/',',',$line);
      $record = explode("./", $clean_line);
      $line = fgets($stream);

      // replaced unnecessary characters in routes so that paths are valid
      $fixed_url = preg_replace('/\/index/', '', $record[1]);
      $fixed_url = preg_replace('/.html,/','', $fixed_url);
      $fixed_url = preg_replace('/\/feed/', '', $fixed_url);
      echo "<p><a href=\"http://$fixed_url\">".$line."</a></p>\n";
   }

   fclose($stream);
   
   exec("rm recommendations.py");
   exec("rm query.py");
   exec("rm output");
   exec("rm recs");
}
?>

</div>
</main>
<div class="footer"> 
	<a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">
	<img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc/4.0/88x31.png">
	</a>
	<br>This work is licensed under a 
	<a rel="license" href="http://creativecommons.org/licenses/by-nc/4.0/">
		Creative Commons Attribution-NonCommercial 4.0 International License.</a>	
</div>


</body>
</html>
