# ECOAGRI – Smart Eco-Agriculture Platform

## Overview
ECOAGRI is a web-based platform for sustainable agriculture management. It connects users, waste management and auctions, and advice publications with replies.

This project was developed at **Esprit School of Engineering – Tunisia** (Academic Year 2026–2027).

## Features
- User registration and authentication
- Waste management and auctions
- Publications management (add, edit, delete, list)
- Replies management for each publication
- Dashboard for administration
- Responsive front office and back office

## Tech Stack

### Frontend
- HTML5
- CSS3
- JavaScript

### Backend
- PHP 8

### Database
- MySQL

### Development Tools
- Git
- GitHub
- Visual Studio Code
- XAMPP / Apache Server

## Architecture
The application follows a **Client-Server Architecture** based on the **MVC (Model-View-Controller)** pattern.

### Model
Handles database operations using MySQL.

### View
Manages the user interface using HTML, CSS, and JavaScript.

### Controller
Handles business logic and communication between the Model and View using PHP.

### Workflow
1. The user interacts with the interface
2. The request is sent to the Controller
3. The Controller processes the logic
4. The Model accesses the database
5. The response is returned to the user

### Project Structure
- `ecoagri/` : main application (MVC)
- `gestion-dechets/` : waste management module
- `gestion-publications/` : publications and replies module
- `utilisateur/` : user module

## Contributors
- Ons Sahli ([@ons7vd](https://github.com/ons7vd))
- Laith Mansour ([@laithmansour27](https://github.com/laithmansour27))
- Emna Cheikh
- Sami Klouz
- Med Amine Khlifi
- Yassine Tijeni

## Academic Context
Developed as part of the PI – 2nd Year Engineering Program at **Esprit School of Engineering – Tunisia**
PI – 2A11 | Academic Year 2026–2027

## Getting Started

### Installation
````bash
git clone https://github.com/laithmansour27/Esprit--PI---2A11---AU---ECOAGRI-.git
cd Esprit--PI---2A11---AU---ECOAGRI-
````

### Run
1. Place the project in `C:\xampp\htdocs`
2. Start Apache and MySQL from XAMPP
3. Import `ecoagri/ecoagri/database/schema.sql` in phpMyAdmin
4. Open `http://localhost/Esprit--PI---2A11---AU---ECOAGRI-/ecoagri/ecoagri/`

## Acknowledgments
Thanks to our tutors at Esprit School of Engineering for their guidance and support.
````
````

