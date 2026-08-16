[autocare-iteration-2-README.md](https://github.com/user-attachments/files/31122780/autocare-iteration-2-README.md)
# AutoCare – Vehicle Service Management System

AutoCare is a web-based vehicle service management and booking system built using PHP and MySQL.

The application allows users to register and log in, manage their vehicles, book vehicle service appointments, select available time slots, and view their bookings through an interactive web interface.

---

## Features

### User Authentication
- User registration
- User login and logout
- Session management
- Protected pages for authenticated users

### Vehicle Management
- Add vehicles
- View registered vehicles
- View vehicle details
- Manage multiple vehicles under a user account

### Service Booking
- Select a vehicle for servicing
- Choose a service date
- View available time slots dynamically
- Book a vehicle service appointment
- Booking confirmation

### Dynamic Functionality
- AJAX-based dynamic data loading
- Dynamic vehicle information selection
- Available service slot handling
- Interactive booking process

---

## Technologies Used

- **PHP** – Backend development
- **MySQL** – Database management
- **HTML5** – Website structure
- **CSS3** – Styling and layout
- **JavaScript** – Client-side functionality
- **AJAX** – Dynamic interactions and data loading
- **Python** – Image processing and utility scripts
- **XAMPP** – Local development environment

---

# Installation and Setup

## 1. Clone the Repository

```bash
git clone https://github.com/Mr-Boombastic9116/autocare-iteration-2.git
```

Alternatively, download the repository as a ZIP file and extract it.

## 2. Move the Project to XAMPP

Place the project folder inside the XAMPP `htdocs` directory.

Example:

```text
C:\xampp\htdocs\autocare
```

## 3. Start Apache and MySQL

Open the **XAMPP Control Panel** and start:
- Apache
- MySQL

## 4. Set Up the Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Create the required database and import:

```text
databases/autocare_final.sql
```

> `autocare_final.sql` should be used as the main database file for the latest version of the project.

## 5. Configure the Database Connection

Open:

```text
includes/db.php
```

Update the database configuration if necessary.

Example:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "your_database_name";
```

## 6. Run the Application

Open:

```text
http://localhost/autocare/
```

---

## How to Use

1. Create an account or log in.
2. Add your vehicle details.
3. View your registered vehicles.
4. Select a vehicle for servicing.
5. Choose a service date and available time slot.
6. Confirm the booking.
7. View your service bookings.

---

## Main Pages

| Page | Description |
|---|---|
| `index.php` | Main entry page |
| `signup.php` | User registration |
| `home.php` | User dashboard |
| `add_vehicle.php` | Add a new vehicle |
| `vehicles.php` | View registered vehicles |
| `vehicle_details.php` | View detailed vehicle information |
| `book_service.php` | Book a vehicle service |
| `bookings.php` | View service bookings |
| `confirmation.php` | Display booking confirmation |
| `logout.php` | Log out the current user |

---

## AJAX Functionality

The `ajax/` folder handles:

- Vehicle models
- Fuel types
- Vehicle variants
- Available years
- Available service slots
- Saving service bookings

---

## Python Utility Scripts

The repository also includes:

- `make_superb_vehicles.py`
- `process_all_white_bg.py`
- `process_images.py`

These scripts are used for image processing and project-related utilities.

---

## Future Improvements

- Admin dashboard
- Service status tracking
- Service history
- Email or SMS notifications
- Online payment integration
- Vehicle service reminders
- Enhanced mobile responsiveness
- User profile management
- Service center management

---

## Important Notes

- This project is intended for educational and learning purposes.
- XAMPP is required to run the project locally.
- Make sure Apache and MySQL are running before opening the application.
- Database settings may need to be updated depending on your local environment.
- Do not upload real passwords, API keys, or other sensitive credentials to a public repository.

---

## Author

Developed as a web development project.

GitHub Profile:  
https://github.com/Mr-Boombastic9116

---

## License

This project is currently intended for educational and learning purposes.
