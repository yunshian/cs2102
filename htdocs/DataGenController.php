<?php

class DataGenController {
    public function generateData()
    {
        // Set up filenames
        $filenames = array();
        $filenames["categories"] = "categories.txt"; // Item categories

        // Item qualifiers for each item category
        $filenames["furniture_qualifiers"] = "furniture_qualifiers.txt";
        $filenames["stationery_qualifiers"] = "stationery_qualifiers.txt";
        $filenames["clothes_qualifiers"] = "clothes_qualifiers.txt";
        $filenames["books_qualifiers"] = "book_qualifiers.txt";

        // Item lists
        $filenames["furniture"] = "furniture.txt";
        $filenames["stationery"] = "stationery.txt";
        $filenames["clothes"] = "clothes.txt";
        $filenames["books"] = "book.txt";

        // Read data from disk
        foreach ($filenames as $key => $filename) {
            $handle = fopen("../items/$filename", "r");
            $$key = array();

            if ($handle) {
                while (($buffer = fgets($handle, 4096)) !== false) {
                    ${$key}[] = rtrim($buffer);
                }
                if (!feof($handle)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($handle);
            }
        }

        // Generate random items
        $faker = Faker\Factory::create();
        date_default_timezone_set("Singapore");

        $numListings = 100;
        $numUsers = 20; // Hardcoded for now
        $sql = "";

        for ($i = 1; $i <= $numListings; $i++) {
            // Select a random item category
            $category = $categories[mt_rand(0, count($categories) - 1)];

            // Select a random item from that category
            $item = ${$category}[mt_rand(0, count($$category) - 1)];

            // Select a random qualifier for that item
            $qualifier = ${$category . "_qualifiers"}[mt_rand(0, count(${$category . "_qualifiers"}) - 1)];
            
            // Create the sql statement
            $sql = $sql . "INSERT INTO listings (ownerId, name, description, category, pickupLocation, returnLocation, pickupDate, returnDate, minPrice, status) VALUES (";
            $sql = $sql . mt_rand(1, $numUsers) . ", "; // Owner id
            $sql = $sql . "'" . $qualifier . " " . $item . "'" . ", "; // Item name
            $sql = $sql . "'" . $faker->sentence . "'" . ", "; // Description
            $sql = $sql . "'" . $category . "'" . ", ";
            $sql = $sql . "'" . str_replace(["'", '"'], "", $faker->address()) . "'" . ", "; // Pick up location
            $sql = $sql . "'" . str_replace(["'", '"'], "", $faker->address()) . "'" . ", "; // Return location
            $sql = $sql . "'" . $faker->dateTimeBetween('now', '+ 5 days')->format('Y-m-d') . "'" . ", "; // Pickup date
            $sql = $sql . "'" . $faker->dateTimeBetween('+ 6 days', '+ 10 days')->format('Y-m-d') . "'" . ", "; // Return date
            $sql = $sql . mt_rand(0, 15) . ", "; // Min price
            $sql = $sql . "'" . "open" . "'" . ");\n"; // Hardcoded for now

        }

        // Write the sql statements to an sql script file
        file_put_contents("../sql/listings.sql", $sql);

        echo 'Data successfully generated!';
    }

    public function generateUserData()
    {
        // Generate random items
        $faker = Faker\Factory::create();

        $numUsers = 20; // Hardcoded for now
        $sql = "";

        // Create an admin account 
        $hashedPassword = password_hash('adminpassword', PASSWORD_DEFAULT);
        $sql = $sql . "INSERT INTO users (username, password, userType, bidPts) VALUES ('admin', '$hashedPassword', 'admin', 1000);\n";

        for ($i = 2; $i <= $numUsers; $i++) {
            // Create the sql statement
            $hashedPassword = password_hash($faker->password, PASSWORD_DEFAULT);
            $sql = $sql . "INSERT INTO users (username, password, userType, bidPts) VALUES (";
            $sql = $sql . "'" . $faker->userName . "'" . ", "; // Username
            $sql = $sql . "'" . $hashedPassword . "'" . ", "; // Password
            $sql = $sql . "'" . "normal". "'" . ", "; // Normal user
            $sql = $sql . "'" . 1000 . "'" . ");\n"; // Bidding points
        }

        // Write the sql statements to an sql script file
        file_put_contents("../sql/users.sql", $sql);

        echo 'Data successfully generated!';
    }
}