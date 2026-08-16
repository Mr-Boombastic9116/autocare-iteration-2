# AutoCare – Vehicle Service Management System

AutoCare is a web-based vehicle service management and booking system built using PHP and MySQL.

The application allows users to register and log in, manage their vehicles, book vehicle service appointments, select available time slots, and view their bookings through an interactive web interface.

The system uses AJAX for dynamic interactions and MySQL for storing and managing user, vehicle, service, and booking information.

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

## Project Structure

```text
autocare-iteration-2/
│
├── ajax/
│   ├── get_fuels.php
│   ├── get_models.php
│   ├── get_slots.php
│   ├── get_variants.php
│   ├── get_years.php
│   └── save_booking.php
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   ├── images_backup/
│   └── assets/
│
├── databases/
│   ├── autocare old.sql
│   ├── autocare updated.sql
│   ├── autocare.sql
│   └── autocare_final.sql
│
├── includes/
│   ├── auth.php
│   ├── db.php
│   ├── footer.php
│   ├── header_app.php
│   ├── services_data.php
│   └── vehicle_image.php
│
├── add_vehicle.php
├── book_service.php
├── bookings.php
├── confirmation.php
├── header_app.php
├── home.php
├── index.php
├── logout.php
├── signup.php
├── vehicle_details.php
├── vehicles.php
│
├── make_superb_vehicles.py
├── process_all_white_bg.py
├── process_images.py
│
└── README.md
