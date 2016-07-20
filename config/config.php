<?php
    define("URL", "localhost/WebUtilieis/");
    define("ROOT", "");
    define("DB_TYPE", "mysql");
    define("DB_HOST", "localhost");
    define("DB_NAME", "test");
    define("DB_USER", "root");
    define("DB_PASS", "");
    
    define("GENERATED", "vendor/");
    define("GENERATED_CLASSES", GENERATED."classes/");
    define("GENERATED_MODELS", GENERATED."models/");
    define("GENERATED_CRUD", GENERATED."crud/");
    define("GENERATED_FAKE_DATA", GENERATED."fake/");

    define("FAKE_NAMES", serialize([
        "Dupond","Omega"
    ]));

    define("FAKE_SURNAMES", serialize([
        "Rugal", "Mary", "Jean", "Paul", "Bernard", "Herman", "Yan"
    ]));

    define("FAKE_LABELS", serialize([
        "Libelle 1", "Libelle 2", "Libelle 3"
    ]));

    define("FAKE_DESCRIPTIONS", serialize([
        "Lorem Ipsun", "My Fake Description"
    ]));

    define("FAKE_IMAGES", serialize([
        "image1.png", "photo.jpg"
    ]));


