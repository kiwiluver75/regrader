 <?php 
 $res = 'java';
 $filpath =  '/afs/csl.tjhsst.edu/web/www/studentlife/activ/ict/regrader/secret-f370b55ecc806a1cb033a793509e50b6/8/source/';
  if($res == 'java')
                  {
		                          $cmdo = $filpath.'*.java';
					  $strong = shell_exec("echo $(basename $cmdo)");
								                  }
 if( shell_exec("echo -n $(basename secret-f370b55ecc806a1cb033a793509e50b6/8/source/*.java)" ==  "add.java")) echo "yay";
  //echo file_get_contents("\"$filpath$strong\"");
  //echo file_get_contents("/afs/csl.tjhsst.edu/web/www/studentlife/activ/ict/regrader/secret-f370b55ecc806a1cb033a793509e50b6/8/source/add.java");


   ?> 
