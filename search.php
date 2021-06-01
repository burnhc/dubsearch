<html>
<head>
	<title>UW Search and Recommender System</title>
</head>

<body>

<h2>Explore the University of Washington Collection</h2>
<p>Search for University of Washington documents in this collection.</p>
<p>Try these queries: 'ischool', 'coronavirus', 'alumni', 'technology'</p>
<form action="search.php" method="post">
<input type="text" size=40 name="search_string" value="<?php echo $_POST["search_string"];?>"/>
<input type="submit" value="Search"/>
</form>

<?php

if (isset($_POST["search_string"]))
{
   $search_string = $_POST["search_string"];
   $qfile = fopen("query.py", "w");

   $log_file = fopen("log.txt" "w");
   $log_entry = $_SERVER["REMOTE_ADDR"] . "," . $search_string . "\n";

   fwrite($log_file, $log_entry);

   fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
   fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
   fwrite($qfile, "index = pt.IndexFactory.of(\"./uw_index/data.properties\")\n");
   fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n");

   for ($i=0; $i<10; $i++)
   {
      fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",tf_idf.transform(queries).docid[$i]))\n");
      fwrite($qfile, "print(index.getMetaIndex().getItem(\"title\",tf_idf.transform(queries).docid[$i]))\n");
   }
   
   fclose($log_file);
   fclose($qfile);

   exec("ls | nc -u 127.0.0.1 10010");
   sleep(3);

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
	echo "<a href=\"http://$fixed_url\">".$line."</a><br/>\n";
   }

   fclose($stream);
   
   exec("rm query.py");
   exec("rm output");
}
?>

</body>
</html>
