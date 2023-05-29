<?php
// Paramètres de connexion à la base de données
$serveur = 'localhost';
$nomUtilisateur = 'root';
$motDePasse = '';
$nomBaseDeDonnees = 'score';

try {
    $db = new PDO("mysql:host=$serveur;dbname=$nomBaseDeDonnees", $nomUtilisateur, $motDePasse);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}


function analyzeSentiment($commentaire) {
    // Liste de mots positifs
    $positiveWords = array(
        "heureux", "merveilleux", "superbe", "excellent", "admirable", "génial", "chanceux", "aimer", "joyeux", "brillant",
        "enthousiaste", "énergique", "radieux", "vibrant", "triomphant", "glorieux", "réjouissant", "épanoui", "intéressant",
        "extraordinaire", "fantastique", "fabuleux", "éblouissant", "splendide", "divin", "généreux", "charismatique",
        "captivant", "gracieux", "irrésistible", "exaltant", "formidable", "remarquable", "ravissant", "fascinant", "victorieux",
        "envoûtant", "magnifique", "enthousiasmant", "réconfortant", "adorable", "aimable", "séduisant", "élégant", "souriant",
        "optimiste", "bienveillant", "festif", "jubilant", "inspirant", "enchanteur", "heureux", "félicitations", "fierté",
        "dynamique", "spectaculaire", "innovant", "rayonnant", "enthousiaste", "prometteur", "enthousiasmant", "créatif",
        "splendide", "épatant", "harmonieux", "impressionnant", "encourageant", "sublime", "génialissime", "étonnant", "magique",
        "captivant", "remarquable", "bouleversant", "excitant", "extra", "féérique", "génial", "grandiose", "plaisant",
        "rassurant", "intense", "vivifiant", "exquis", "étonnant", "intéressant", "exhilarant", "stimulant", "reconnaissant"
    );

    // Liste de mots négatifs
    $negativeWords = array(
        "triste", "terrible", "mauvais", "décevant", "horrible", "ennuyeux", "douloureux", "colère", "regretter", "affreux",
        "fatigué", "désespéré", "irrité", "déprimé", "désastreux", "catastrophique", "honteux", "inacceptable", "atroce",
        "inquiétant", "détestable", "exaspérant", "injuste", "insupportable", "déplorable", "vulnérable", "mortifiant",
        "déprimant", "insatisfaisant", "lugubre", "malheureux", "stressant", "anxieux", "désolant", "frustrant", "odieux",
        "terrifiant", "épouvantable", "accablant", "tragique", "lamentable", "misérable", "indigné", "mécontent", "consternant",
        "désespérant", "effrayant", "révoltant", "sombre", "ignoble", "injustifié", "funeste", "navrant", "pénible", "horrifiant",
        "effarant", "inacceptable", "affligeant", "douloureux", "amer", "agonisant", "insensible", "irrespectueux", "dévastant",
        "désagréable", "effrayant", "effroyable", "pathétique", "sordide", "repoussant", "démoralisant", "ennuyeux", "lamentable",
        "alarmant", "dégoutant", "dévalorisant", "discourager", "inadapté", "indigne", "injustifié", "énervant", "paralysant",
        "humiliant", "stupide", "inutile", "abominable", "discriminant", "répréhensible", "troublant", "accablant", "méprisable",
        "maussade", "ridicule", "effrayant", "irritant", "troublant", "absurde", "ennuyeux", "confus", "déstabilisant", "démodé"
    );
    $commentaire = strtolower($commentaire);
    $mots = explode(" ", $commentaire);

    $positivite = 0;
    $negativite = 0;

    foreach ($mots as $mot) {
        if (in_array($mot, $positiveWords)) {
            $positivite++;
        } elseif (in_array($mot, $negativeWords)) {
            $negativite--;
        }
    }
    return $positivite + $negativite;
}


if(isset($_POST['ok'])){
    if(isset($_POST['commentaire'])){
        if(!empty($_POST['commentaire'])){
            $id = $_GET['id'];
            $commentaire = $_POST['commentaire'];
            $score = analyzeSentiment($commentaire);

            $req = $db->prepare('SELECT score FROM restaurant WHERE id = ?');
            $req->execute(array($id));
            $restaurant = $req->fetch();

            $scoreFinal = $score + $restaurant['score'];

            $req = $db->prepare('UPDATE restaurant SET score=? WHERE id = ?');
            $req->execute(array($scoreFinal,$id));
            header("Location: index.php");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet 2</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body style="background-color: #111827;">
    <nav class="navbar navbar-dark" style="background-color: #030712;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Projet 2</span>
        </div>
    </nav>
    <div class="container mt-5">
    <?php
        if(!isset($_GET['url'])){
    ?>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
                            class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                            aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                            aria-label="Slide 3"></button>
                    </div>
                    <div class="carousel-inner">
                        <?php
                            
                            $req = $db->query('SELECT * FROM restaurant');
                            $restaurants = $req->fetchAll();
                            foreach($restaurants as $restaurant){
                        ?>
                            <div class="carousel-item <?= ($restaurant['id'] == 1) ? "active" : "" ?>">
                                <img src="<?= $restaurant['image'] ?>" class="d-block w-100" alt="...">
                                <div class="carousel-caption d-none d-md-block bg-light text-dark">
                                    <h5>Score: <span style="color:#059669;"><?= $restaurant['score'] ?></span></h5>
                                    <a href="index.php?url=comment&id=<?= $restaurant['id'] ?>" class="btn btn-success">commenter</a>
                                </div>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
        <?php }else{
        ?>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form action="" method="POST">
                    <div class="form">
                        <textarea class="form-control" cols="10" rows="5" name="commentaire" placeholder="Laissez um commentaire"></textarea>
                        <label for="floatingTextarea">commentaire</label>
                    </div>
                    <button type="submit" class="btn btn-success mt-2 ms-auto d-block" name="ok">sumbit</button>
                </form>
            </div>
        </div>
        <?php
        } ?>
    </div>

    <script src="js/bootstrap.min.js"></script>
</body>

</html>