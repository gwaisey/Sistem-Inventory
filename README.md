# FIFO Inventory Management System

A web-based inventory application built with **Laravel** to manage stock movement using the **First-In, First-Out (FIFO)** method. This system ensures that the oldest stock batches are depleted first during outgoing transactions to maintain chronological inventory accuracy and proper stock valuation.

## 🚀 Features

* **Stock Maintenance**: A centralized interface to record "In" (Masuk) and "Out" (Keluar) transactions.
* **FIFO Engine**: Automatically identifies and deducts from the earliest available stock batches.
* **Real-time Logging**: Every transaction is recorded with a timestamp, quantity, program source, and user ID.
* **Normalized Data**: Uses a structured database approach to manage Items and Locations independently, preventing data redundancy.
* **Advanced Reporting**:
    * **Stock Balance Report**: View current inventory levels filtered by Location and Item Code.
    * **Transaction History**: A full audit trail with filtering by Proof Number, Date, Location, and Item Code.

## 📋 Business Rules & Validations

Based on the project specifications:
* **FIFO Logic**: When reducing stock, the system consumes the full balance of the oldest batch (based on entry date) before moving to the next available batch.
* **Date Constraint**: Incoming and outgoing transactions are rejected if the transaction date is earlier than the last recorded entry for that specific item and location.
* **Balance Check**: "Out" transactions are rejected if the requested quantity exceeds the total available balance in the selected location.



## 🛠️ Tech Stack

* **Framework**: Laravel 11
* **Database**: MySQL
* **Frontend**: Bootstrap 5 & Blade Templating
* **Scripting**: jQuery & AJAX (for dynamic reporting)

## ⚙️ Installation

1.  **Clone the repository**:
    ```bash
    git clone [https://github.com/gwaisey/Sistem-Inventory.git](https://github.com/gwaisey/Sistem-Inventory.git)
    cd Sistem-Inventory
    ```

2.  **Setup Environment**:
    * Copy `.env.example` to `.env`
    * Update your database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD) in `.env`
    * Run `php artisan key:generate`

3.  **Install & Migrate**:
    ```bash
    composer install
    php artisan migrate
    ```

4.  **Run Application**:
    ```bash
    php artisan serve
    ```
