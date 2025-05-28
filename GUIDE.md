# Installation Guide

Welcome to the ACME Corp Donation Platform! Follow these steps to get the application up and running locally using
Docker.

## Prerequisites

- Docker and Docker Compose installed on your system
- Git

## Installation Steps

1. **Clone the Repository**

   ```bash
   git clone git@github.com:68deaffd-5cdb-417e-9439-7fb678b4546f/bebc7a30-7b8b-4861-b3d6-5509d9ee804c.git
   cd bebc7a30-7b8b-4861-b3d6-5509d9ee804c
   ```

2. **Start the Application**

   Launch the full stack (Laravel API, Keycloak, and Next.js frontend) using Docker:

   ```bash
   docker-compose up
   ```

   > **Note:** The first boot may take 3–5 minutes to initialize all containers and services. Wait until logs stop
   scrolling rapidly before proceeding.

3. **Access the Application**

   Open your browser and visit:

   http://127.0.0.1

   If everything is configured properly, you will be redirected to the Keycloak login page.


4. **Login Credentials**

   Use the following demo credentials:

    - **Username:** `demo`
    - **Password:** `demo`


5. **First Login Setup**

   After logging in, you’ll be prompted to fill in:

    - Email
    - First Name
    - Last Name

   These details are managed via Keycloak.


6. **Authorization**

   After entering your details, Keycloak will redirect you to the Laravel application, which will request authorization.

    - Click the **"Authorize"** button to complete the login flow.


7. **Access the Frontend**

   After successful authorization, you will be redirected back to:

   http://127.0.0.1

   You should now see the **Next.js frontend**. From here, you can:

    - Create campaigns
    - List your campaigns
    - Donate to existing campaigns


8. **API Documentation**

   The Laravel API Swagger documentation is available at:

http://127.0.0.1:9004/api
