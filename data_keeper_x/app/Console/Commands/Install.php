<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install DataKeeperX Application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Print License
        $this->line('Welcome to <fg=magenta;>DataKeeperX</>');
        $this->newLine();

        // Copy .env file
        if (!$this->copyDotEnv()) {
            return 0;
        }

        // Ask User Token LifeTime
        $tokenLifeTime = $this->askAccessTokenLifeTime();

        // Ask Database Configs
        $db_connection = $this->askDatabaseConnection();
        $db_host = $this->askDatabaseHost();
        $db_port = $this->askDatabasePort();
        $db_name = $this->askDatabaseName();
        $db_username = $this->askDatabaseUsername();
        $db_password = $this->askDatabaseUserPassword();

        // Update fields
        $this->updateEnvFile('ACCESS_TOKEN_EXPIRE', $tokenLifeTime);
        $this->line("Update AccessTokenLifeTime <fg=green;>Successfully!</>");
        $this->updateEnvFile('DB_CONNECTION', $db_connection);
        $this->updateEnvFile('DB_HOST', $db_host);
        $this->updateEnvFile('DB_PORT', $db_port);
        $this->updateEnvFile('DB_DATABASE', $db_name);
        $this->updateEnvFile('DB_USERNAME', $db_username);
        $this->updateEnvFile('DB_PASSWORD', $db_password);
        $this->line("Update Database Configuration <fg=green;>Successfully!</>");

        // generate app key
        $output = new BufferedOutput();
        Artisan::call('key:generate', [], $output);
        $this->line("Generated AppKEY is <fg=green;>" . env('APP_KEY') . "</>");

        // generate JWT key
        $output = new BufferedOutput();
        Artisan::call('jwt:generate', [], $output);
        $this->line("Generated JWT is <fg=green;>" . env('JWT_SECRET') . "</>");

        // Run Migration
        $this->newLine();
        $this->line("run Migration...");
        $this->call("db:wipe");
        $this->call("migrate");

        $this->line("<fg=green;>App Initalizing Successfully!</>");
    }

    /**
     * Copy the .env.example file to .env.
     *
     * This method checks if the source file (.env.example) exists. If it does not exist,
     * it logs an error and returns false. If the destination file (.env) already exists,
     * the method assumes the environment file has already been set up and returns true.
     * If neither of these conditions is met, the source file is copied to the destination,
     * and the method returns true.
     *
     * @return bool Returns true if the file was copied or if it already exists;
     *              returns false if the source file does not exist.
     */
    private function copyDotEnv(): bool
    {
        $source = base_path('.env.example');
        $destination = base_path('.env');

        if (!File::exists($source)) {
            $this->error('.env.example does not exist.');
            return false;
        }

        if (File::exists($destination)) {
            return true;
        }

        File::copy($source, $destination);

        return true;
    }

    /**
     * Update a specific variable in the .env file.
     *
     * This method updates the value of a specified environment variable in the
     * .env file. It first checks if the .env file exists. If the file does not
     * exist or if the variable is not found, it returns false. Otherwise, it updates
     * the variable's value and returns true.
     *
     * @param string $key   The environment variable key to update.
     * @param string $value The new value for the environment variable.
     * @return bool         Returns true if the variable was updated successfully;
     *                      returns false if the file does not exist or the variable was not found.
     */
    private function updateEnvFile(string $key, string $value): bool
    {
        $path = base_path('.env');

        // Check if the .env file exists
        if (!File::exists($path)) {
            $this->error('.env file does not exist.');
            return false;
        }

        // Read the .env file into an array
        $lines = File::lines($path);
        $updated = false;

        // Open the .env file for writing
        $newContent = '';
        foreach ($lines as $line) {
            // Check if the line starts with the specified key
            if (strpos($line, "$key=") === 0) {
                // Replace the line with the new key=value pair
                $newContent .= "$key=$value" . PHP_EOL;
                $updated = true;
            } else {
                // Keep the existing line unchanged
                $newContent .= $line . PHP_EOL;
            }
        }

        // If the key was not found, append the new key=value pair
        if (!$updated) {
            $newContent .= "$key=$value" . PHP_EOL;
        }

        // Write the updated content back to the .env file
        File::put($path, $newContent);

        return true;
    }

    /**
     * Prompt the user to select a database connection from a predefined list.
     *
     * This method displays a prompt to the user asking them to select one of the available
     * database connection options. It validates the user's input to ensure it is one of
     * the allowed options. If the input is invalid, the user is re-prompted until a valid
     * selection is made.
     *
     * @return string The selected database connection option.
     */
    private function askDatabaseConnection(): string
    {
        $db_connection_options = ['mysql', 'sqlite'];

        while (true) {
            $db_connection = $this->askWithCompletion('Please select Database Connection', $db_connection_options, 'mysql');

            // check value
            if (in_array($db_connection, $db_connection_options)) {
                break;
            }

            $this->error("Invalid Connection!");
        }

        return $db_connection;
    }

    /**
     * Prompt the user to enter a valid database host.
     *
     * This method repeatedly asks the user to provide a database host IP address.
     * It validates the IP address and ensures it is in a correct format before accepting it.
     *
     * @return string The entered database host IP address.
     */
    private function askDatabaseHost()
    {
        while (true) {
            $response = $this->ask('Please enter Database host', "127.0.0.1");

            // check value
            if ($this->isValidIP($response)) {
                break;
            }

            $this->error("Invalid Host!");
        }

        return $response;
    }

    /**
     * Prompt the user to enter a valid database port number.
     *
     * This method repeatedly asks the user to provide a database port number.
     * It validates that the port number is within the valid range (1 to 99999) before accepting it.
     *
     * @return int The entered database port number.
     */
    private function askDatabasePort()
    {
        while (true) {
            $response = $this->ask('Please enter Database port', 3306);

            // check value
            if ($response > 1 && $response < 99999) {
                break;
            }

            $this->error("Invalid Port!");
        }

        return $response;
    }

    /**
     * Prompt the user to enter the database name.
     *
     * This method asks the user to provide a database name.
     * It sets a default value of "datakeeperx" if the user does not provide an input.
     *
     * @return string The entered database name.
     */
    private function askDatabaseName()
    {
        return $this->ask('Please enter Database name', "datakeeperx");
    }

    /**
     * Prompt the user to enter the database username.
     *
     * This method asks the user to provide a database username.
     * It sets a default value of "dkx_user" if the user does not provide an input.
     *
     * @return string The entered database username.
     */
    private function askDatabaseUsername()
    {
        return $this->ask("Please enter Database Username", "dkx_user");
    }

    /**
     * Prompt the user to enter a database password.
     *
     * This method repeatedly asks the user to provide a database password.
     * It validates that the password is not empty before accepting it.
     *
     * @return string The entered database password.
     */
    private function askDatabaseUserPassword()
    {
        while (true) {
            $password = $this->ask("Please enter Database Password");
            if ($password == '' || empty($password) || is_null($password)) {
                $this->error("Password Not Set!");
                continue;
            }
            break;
        }
        return $password;
    }

    /**
     * Check if a given string is a valid IP address (IPv4 or IPv6).
     *
     * @param string $ip The IP address to check.
     * @return bool True if the IP is valid; otherwise, false.
     */
    function isValidIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * Prompt the user to enter the access token lifetime in seconds.
     *
     * This method repeatedly asks the user to provide the lifetime of a user token in seconds.
     * It ensures that the provided value is a positive integer. If the value is invalid,
     * the user is re-prompted until a valid value is entered.
     *
     * @return int The entered access token lifetime in seconds.
     */
    function askAccessTokenLifeTime()
    {
        while (true) {
            $response = $this->ask("Please enter user token life time(second)", 86400);
            if ($response <= 0) {
                $this->error("Invalid LifeTime!");
                continue;
            }
            return $response;
        }
    }
}
