<?php
class MysqlPdoDump{
    private $bdd = null;
    
    function setBdd($bdd){
        $this->bdd = $bdd;
    }
    
    function dumpBdd($mode){
        $entete = "-- ----------------------\n";
        $entete .= "-- dump of the database ".date("d-M-Y")."\n";
        $entete .= "-- ----------------------\n\n\n";
        $creations = "";
        $insertions = "\n\n";

        $listeTables = $bdd->query("SHOW TABLES");
        while($table = $listeTables->fetch())
        {
            // si l'utilisateur a demandÃ© la structure ou la totale
            if($mode == 1 || $mode == 3)
            {
                $creations .= "-- -----------------------------\n";
                $creations .= "-- creation de la table ".$table[0]."\n";
                $creations .= "-- -----------------------------\n";
                $listeCreationsTables = $bdd->query("SHOW CREATE TABLE ".$table[0]);
                while($creationTable = $listeCreationsTables->fetch())
                {
                $creations .= $creationTable[1].";\n\n";
                }
            }
            // si l'utilisateur a demandÃ© les donnÃ©es ou la totale
            if($mode > 1)
            {
                $donnees = $bdd->query("SELECT * FROM ".$table[0]);
                $insertions .= "-- -----------------------------\n";
                $insertions .= "-- insertions dans la table ".$table[0]."\n";
                $insertions .= "-- -----------------------------\n";
                while($nuplet = $donnees->fetch())
                {
                    $insertions .= "INSERT INTO ".$table[0]." VALUES(";
                    for($i=0; $i < $donnees->columnCount(); $i++)
                    {
                    if($i != 0)
                        $insertions .=  ", ";
                    if($donnees->getColumnMeta($i) == "string" || $donnees->getColumnMeta($i) == "blob")
                        $insertions .=  "'";
                    $insertions .= addslashes($nuplet[$i]);
                    if($donnees->getColumnMeta($i) == "string" || $donnees->getColumnMeta($i) == "blob")
                        $insertions .=  "'";
                    }
                    $insertions .=  ");\n";
                }
                $insertions .= "\n";
            }
        }
        if($mode == 1){
            $nomFichier = "dump_structure_" . date('d-m-Y') . '.sql';
        }else if($mode == 2){
            $nomFichier = "dump_data_" . date('d-m-Y') . '.sql';
        }else if($mode == 3){
            $nomFichier = "dump_all_" . date('d-m-Y') . '.sql';
        }
        $fichierDump = fopen($nomFichier, "wb");
        fwrite($fichierDump, $entete);
        fwrite($fichierDump, $creations);
        fwrite($fichierDump, $insertions);
        fclose($fichierDump);
        //Ecriture des logs
        $data = "Date: " . date('d:m:Y H:i:s') . " :\r\n";
        $data .= "Ip Adress : " . $_SERVER['REMOTE_ADDR'] . " -> Id of users: " . $_SESSION['idUser'] . "\r\n";
        $data .= "Mode : " . $mode . "\r\n \r\n";
        $fichierDump = fopen("LogDump.txt", "a");
        fwrite($fichierDump, $data);
        fclose($fichierDump);

        header('Location: ' . $nomFichier);
    }
}
