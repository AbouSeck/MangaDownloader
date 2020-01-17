<?php 

if($argc==4){

    $statusCode=0 ;

    //Nom du Manga
    $manga_name =ucfirst($argv[1]) ;

    //Numeros des chapitres
    $chap_deb = $argv[2] ;
    $chap_fin = $argv[3] ;

    for($chap_num=$chap_deb ; $chap_num<=$chap_fin; $chap_num++ ){

    //Page
    $p=1 ;

    //Nom du dossier
    $dir_name =$manga_name.' '.$chap_num ;


    echo  "\n".$dir_name." est en cours de téléchargement..." ;

    $exists = true ;

    while($exists==true){
    //Vérification de l'existence du manga 
    $file = 'https://www.mangapanda.com/'.$argv[1].'/'.$chap_num.'/'.$p;
    $file_headers = @get_headers($file);
    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
        else {
            $exists = true;
        }

        if($exists==true){
            //Recherche de la page     
            $html = file_get_contents('https://www.mangapanda.com/'.$argv[1].'/'.$chap_num.'/'.$p);
            preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matches ); 

            $fileUrl = $matches[ 1 ][0 ];

            //Création du dossier si non existant 
            if (!is_dir($manga_name)) {
                mkdir($manga_name);
                chdir($manga_name) ;
                if (!is_dir($dir_name)) {
                    mkdir($dir_name);
                    chdir('..') ;
                }
            } else {
                chdir($manga_name) ;
                if (!is_dir($dir_name)) {
                mkdir($dir_name);
                }
                chdir('..') ;
            }
            
            //Spécification du fichier 
            $saveTo = $manga_name.'/'.$dir_name.'/'.$p.'.jpg';
                
            //Ouverture du fichier
            $fp = fopen($saveTo, 'w+');
                
            //En cas d'erreur lors de l'ouverture.
            if($fp === false){
                throw new Exception('Could not open: ' . $saveTo);
            }
                
            //Création du cURL
            $ch = curl_init($fileUrl);
                
            
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
            //Délai de 20 secondes
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);

            //Exécution de la requête.
            curl_exec($ch);

            //Exception en cas d'erreur 
            if(curl_errno($ch)){
                throw new Exception(curl_error($ch));
            }
                
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            //Fermeture du cURL
            curl_close($ch);

            //Fermeture du fichier
            fclose($fp);

        }
            $p++ ;
            echo '.' ;
        }
    }
  
    if($statusCode == 200){
        echo "\n Downloaded";
    } else{
        echo "Votre téléchargement n'a pas pu aboutir.";
    }

} else {
    echo "Le nombre d'argument est incorrect\nVeuillez spécifier le nom du manga et les numeros du premier et du dernier chapitre" ;    
}


?>