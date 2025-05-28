
## Architecture Choices, Assumptions, Constraints, and Technical Challenges

### Architecture Choices

Given that ACME Corp is a large organization with over 20,000 employees and an existing identity provider, I designed the solution to simulate a real-world enterprise setup. The system is split into a backend API layer built in **Laravel (PHP)** and a frontend built with **Next.js**.

For **authentication and authorization**, I integrated **Keycloak** as the identity provider to mirror a realistic enterprise-grade SSO setup. Laravel handles communication with Keycloak via **Socialite**, orchestrating the OAuth2 flow. A Next.js frontend acts as a thin client, initiating the login process, redirecting users to Keycloak, and handling the token exchange. The token is then used to authorize all API requests to the Laravel backend.

On the Laravel side, the received token is validated and used to retrieve the user’s role from Keycloak. I used **Spatie Laravel Permission** to map these roles (e.g., `admin`, `manager`, `member`) to granular permissions (`campaign_create`, `campaign_read`, etc.). Role management is centralized in Keycloak for security and consistency, while permission control is handled within Laravel.

To simplify REST API development and documentation, I used **api-platform/laravel**. This provides automatic Swagger documentation generation, route and resource configuration, and a structured approach to building maintainable APIs.

For future scalability in handling multiple payment gateways, I chose **league/omnipay**, a popular abstraction layer that allows easy integration of different payment providers with a unified interface.

The application is containerized using **Docker**, supporting reproducible environments and making it easy to run the entire stack.

### Assumptions

-   Employees will be authenticated via a central identity provider, such as Keycloak, which supports roles and tokens.

-   Role management (e.g., admin, manager, member) is controlled externally via Keycloak.

-   The internal platform is designed for employees only and not external users.

-   The donation and campaign system will be accessed concurrently by a relatively high number of users but not at internet-scale (due to being internal).

-   The payment system was undecided at development time, so modularity and flexibility were prioritized.


### Constraints

-   **Time constraints** limited implementation of some features, such as email confirmations, administrative endpoints (e.g., campaign approval, moderation), and test coverage.

-   The **payment system was not finalized**, which required building an abstract and swappable integration using Omnipay.

-   No production-ready UI was expected, so the **Next.js frontend** is minimal and primarily serves as a proof of concept.

-   The OAuth2 flow had to be mocked without full enterprise infrastructure in place, adding complexity in handling token storage, forwarding, and validation.


### Technical Challenges & Solutions

-   **Keycloak Integration**: Integrating Keycloak with Laravel was a challenge due to limited out-of-the-box support. I used `laravel/socialite` for OAuth2 orchestration and wrote custom logic to handle the token exchange, validation, and role retrieval.

-   **Token Propagation**: Ensuring secure communication between the frontend and backend via token passing required implementing CSRF-safe flows and using secure storage mechanisms in the frontend.

-   **Granular Permissions**: Mapping centralized roles (from Keycloak) to internal permissions in Laravel required combining external role management with internal fine-grained access control using Spatie's package.

-   **Missing Email and Notification Logic**: Queued email notifications for donation confirmations were planned but not implemented due to time limitations. A queue-based system (e.g., Laravel Queues with Redis) would be used for scalability and reliability.

-   **Incomplete Admin Functionality**: The current implementation focuses on the main user flows (creating, listing, and donating to campaigns). Admin operations (approve, reject, delete campaigns) and user browsing of all campaigns are noted as areas for future extension.

-   **Testing and QA**: Automated tests were not implemented due to time constraints, but the architecture is designed with testability in mind, using service classes, request validation, and API-centric design principles.