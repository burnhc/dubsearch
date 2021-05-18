<html>
<head>
	<title>My Simple Search</title>
</head>

<body>

<h2>My Simple Search</h2>

<form action="search.php" method="post">
<input type="text" size=40 name="search_string" value="<?php echo $_POST["search_string"];?>"/>
<input type="submit" value="Search"/>
</form>
<p>This collection is from subreddit r/seattle, you can search a query for title or comments in a post<p>
<?php

if (isset($_POST["search_string"]))
{
   $search_string = $_POST["search_string"];
   $qfile = fopen("query.py", "w");

   fwrite($qfile, "import pyterrier as pt\nif not pt.started():\n\tpt.init()\n\n");
   fwrite($qfile, "import pandas as pd\nqueries = pd.DataFrame([[\"q1\", \"$search_string\"]], columns=[\"qid\",\"query\"])\n");
   fwrite($qfile, "index = pt.IndexFactory.of(\"./reddit_index/data.properties\")\n");
   fwrite($qfile, "tf_idf = pt.BatchRetrieve(index, wmodel=\"TF_IDF\")\n");

   for ($i=0; $i<10; $i++)
   {
      fwrite($qfile, "print(index.getMetaIndex().getItem(\"filename\",tf_idf.transform(queries).docid[$i]))\n");
      fwrite($qfile, "print(index.getMetaIndex().getItem(\"title\",tf_idf.transform(queries).docid[$i]))\n");
   }
   
   fclose($qfile);

   exec("ls | nc -u 127.0.0.1 10019");
   sleep(3);

   $stream = fopen("output", "r");

   $line=fgets($stream);

   while(($line=fgets($stream))!=false)
   {
	$clean_line = preg_replace('/\s+/',',',$line);
	$record = explode("./", $clean_line);
	$line = fgets($stream);
	echo "<a href=\"http://$record[1]\">".$line."</a><br/>\n";
   }

   fclose($stream);
   
   exec("rm query.py");
   exec("rm output");
}
?>

</body>
</html>
