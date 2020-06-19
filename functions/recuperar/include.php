<?php
   $host        = "host=localhost";
   $port        = "port=5433";
   $dbname      = "dbname=postgres";
   $credentials = "user=postgres password=159753456";

   $db = pg_connect( "$host $port $dbname $credentials"  );
   if(!$db){
      echo "Error : Não é possível conectar ao banco de dados\n";
   }
?>