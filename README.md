# Pokémon Team Builder
![image](https://github.com/user-attachments/assets/b048d2b8-8744-4026-801e-261d416133a2)

Pokémon Team Builder is a Laravel web application that allows users to create and manage teams of Generation 1 Pokémon. The application features a complete Pokédex, team building functionality, and user authentication.

## Table of Contents

- Features
- Prerequisites
- Installation
- Configuration
- Running the Application
- Building Frontend Assets
- Troubleshooting
- Contributing
- License

## Features

- ✅ Complete Gen 1 Pokédex: View details of all 151 original Pokémon
- ✅ Team Builder: Create and manage custom teams of up to 6 Pokémon
- ✅ User Authentication: Secure registration, login, and team ownership
- ✅ Responsive UI: Mobile-friendly layout styled with Tailwind CSS
- ✅ PokéAPI Integration: Real-time data from PokéAPI (https://pokeapi.co/)
- ✅ Pixel Sprites: Classic GameBoy-style Pokémon artwork
- ✅ Save, Edit, Remove Teams: Full team management capabilities

## Prerequisites

Ensure you have the following installed:

- PHP >= 8.1 (`php -v`)
- Composer (`composer --version`)
- Node.js >= 18.x (`node -v`)
- npm >= 10.x (`npm -v`)
- MySQL (recommended), PostgreSQL, or SQLite
- Git

## Installation

1. Clone the repository:
   git clone https://github.com/your-username/pokemon-team-builder.git
   cd pokemon-team-builder

2. Install PHP dependencies:
   composer install

3. Install JavaScript dependencies:
   npm install

4. Create environment file:
   cp .env.example .env

5. Generate application key:
   php artisan key:generate

## Configuration

1. Edit the `.env` file with your database credentials:

DB_CONNECTION=mysql  
DB_HOST=127.0.0.1  
DB_PORT=3306  
DB_DATABASE=pokemon_teams  
DB_USERNAME=root  
DB_PASSWORD=

2. Run database migrations:
   php artisan migrate

(Optional) Seed with sample data:
php artisan db:seed

## Running the Application

To start the Laravel development server, run:

php artisan serve

Visit http://localhost:8000 in your browser.

## Building Frontend Assets

To build and watch frontend assets using Vite:

- For development:
  npm run dev

- For production:
  npm run build

## Troubleshooting

- Clear cache:
  php artisan config:clear
  php artisan cache:clear
  php artisan view:clear

- Check file permissions on storage/ and bootstrap/cache/
- Ensure MySQL is running and credentials are correct

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

1. Fork the repo
2. Create your feature branch (`git checkout -b feature/foo`)
3. Commit your changes (`git commit -am 'Add feature'`)
4. Push to the branch (`git push origin feature/foo`)
5. Create a new Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).
