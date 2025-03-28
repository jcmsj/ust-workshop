<p align="center"><a href="https://computradetech.com.ph/" target="_blank"><img src="https://computradetech.com/wp-content/uploads/2023/03/New-Logo-CTP-01.png" width="400" alt="CTP Logo"></a></p>

# **SimpleCRM**

_Customer Relation Management System_

## **Overview**

A lightweight yet powerful Customer Relationship Management (CRM) application designed to streamline customer interactions. Built as a simple CRUD (Create, Read, Update, Delete) system, it helps businesses efficiently manage client information, track interactions, and enhance customer engagement.

---

## **Tech Stack**

-   **Frontend**:
-   **Backend**:
-   **Database**: PostgreSQL
-   **Deployment**: AWS

---

## **Installation & Setup**

### EC2 Provisioning Pre-requisites

-   PHP 8.3
-   Composer
-   Node.js

-   php artisan key:generate

#### Security Groups:

    - Application Load Balancer: ALLOW Inbound Traffic to TCP 80 anywhere IPv4
       - Link it to -> App Security Group: ALLOW Inbound Traffic from ALB SG (All Traffic)
            - Link it to -> RDS Security Group: ALLOW PostgreSQL 5432 from App Security Group

### Creating RDS via Easy Create

    **PostgreSQL**
        - Free-Tier
        - DB-Instance Name = fil
        - Password = rootrootroot (subject to change)

---

## **Contributors**

👤 **Jace**  
👤 **JC**  
👤 **Samantha**  
👤 **Francis**

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
